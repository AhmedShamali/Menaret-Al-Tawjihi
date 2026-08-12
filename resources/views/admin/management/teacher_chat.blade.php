@extends('layouts.app')

@section('content')
<div class="chat-page-wrapper">
    <div class="chat-main-wrapper">
        <!-- الهيدر (رأس المحادثة) -->
        <div class="chat-nav">
            <div class="teacher-meta">
                <div class="avatar-container">
                    <div class="teacher-icon">{{ mb_substr($teacher->name, 0, 1) }}</div>
                    <span class="status-indicator"></span>
                </div>
                <div class="teacher-info">
                    <h6 class="teacher-name">المعلم: {{ $teacher->name }}</h6>
                    <div class="status-text">
                        <span class="dot"></span>
                        محادثة إدارية نشطة
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="close-chat-btn">
                <span class="btn-text">العودة للقائمة</span>
                <span class="btn-icon">🔙</span>
            </a>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-feed" id="chat_messages">
            <div class="text-center my-auto py-5 text-muted">
                <div class="loading-art">💬</div>
                <p>جاري تحميل المحادثة مع المعلم...</p>
            </div>
        </div>

        <!-- شريط الإدخال -->
        <div class="chat-input-bar">
            <div class="input-group-custom">
                <input type="text" id="msg_input"
                       placeholder="اكتب رسالتك للمعلم هنا..."
                       onkeypress="if(event.key === 'Enter') sendReply()"
                       autocomplete="off">
                <button class="send-trigger" onclick="sendReply()" id="btn_send">
                    <span class="send-text">إرسال</span>
                    <i class="send-icon">🚀</i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --admin-bg: #4e73df;
        --teacher-bg: #ffffff;
        --main-bg: #f4f7fe;
        --text-dark: #2d3748;
        --text-light: #718096;
    }

    /* الحاوية الرئيسية */
    .chat-page-wrapper {
        padding: 15px;
        background: var(--main-bg);
        height: calc(100vh - 100px); /* طول متناسب مع الهيدر */
        display: flex;
        flex-direction: column;
    }

    .chat-main-wrapper {
        display: flex;
        flex-direction: column;
        flex: 1;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
        overflow: hidden;
        position: relative;
    }

    /* الهيدر */
    .chat-nav {
        padding: 15px 25px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 10;
    }

    .teacher-meta { display: flex; align-items: center; gap: 12px; }

    .avatar-container { position: relative; }
    .teacher-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2);
    }

    .status-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: #10b981;
        border: 2px solid #fff;
        border-radius: 50%;
    }

    .teacher-name { margin: 0; color: var(--text-dark); font-weight: 700; }
    .status-text { font-size: 0.75rem; color: var(--text-light); display: flex; align-items: center; gap: 5px; }
    .status-text .dot { width: 6px; height: 6px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite; }

    /* منطقة المحادثة */
    .chat-feed {
        flex: 1;
        padding: 25px;
        background: #fdfdfd;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }

    /* الفقاعات */
    .bubble {
        max-width: 75%;
        padding: 12px 18px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        animation: fadeIn 0.3s ease;
    }

    .admin-msg {
        align-self: flex-start; /* لأن الاتجاه RTL، سنبدأ من اليمين للأدمن */
        background: var(--admin-bg);
        color: white;
        border-radius: 20px 20px 4px 20px;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.15);
        margin-right: auto;
    }

    .teacher-msg {
        align-self: flex-end;
        background: #fff;
        color: var(--text-dark);
        border: 1px solid #e2e8f0;
        border-radius: 20px 20px 20px 4px;
        margin-left: auto;
    }

    .msg-date {
        font-size: 10px;
        display: block;
        margin-top: 6px;
        opacity: 0.7;
        text-align: left;
    }

    /* شريط الإدخال */
    .chat-input-bar {
        padding: 20px 25px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
    }

    .input-group-custom {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border-radius: 18px;
        padding: 6px 10px;
        border: 1px solid #e2e8f0;
        transition: 0.3s;
    }

    .input-group-custom:focus-within {
        border-color: var(--admin-bg);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }

    .input-group-custom input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px 15px;
        outline: none;
        font-size: 0.95rem;
        color: var(--text-dark);
    }

    .send-trigger {
        background: var(--admin-bg);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .send-trigger:hover {
        background: #224abe;
        transform: translateY(-1px);
    }

    .send-trigger:active { transform: scale(0.95); }

    /* أزرار العودة */
    .close-chat-btn {
        text-decoration: none;
        color: var(--text-light);
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 12px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .close-chat-btn:hover {
        background: #fff5f5;
        color: #e53e3e;
        border-color: #feb2b2;
    }

    /* التجاوب مع الجوال */
    @media (max-width: 768px) {
        .chat-page-wrapper { padding: 0; height: 100vh; }
        .chat-main-wrapper { border-radius: 0; border: none; }
        .bubble { max-width: 85%; }
        .send-text { display: none; }
        .chat-nav { padding: 12px 15px; }
        .teacher-icon { width: 40px; height: 40px; font-size: 1rem; }
        .btn-text { display: none; }
    }

    /* أنيميشن */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
    .loading-art { font-size: 3rem; margin-bottom: 10px; opacity: 0.3; }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const teacherId = {{ $teacher->id }};
    const feed = document.getElementById('chat_messages');
    let lastContentHash = "";

    function fetchMessages() {
        axios.get(`/admin/teachers/${teacherId}/messages`)
            .then(res => {
                const messages = res.data.messages || [];
                if(messages.length === 0) {
                    feed.innerHTML = '<div class="text-center my-auto py-5 text-muted"><p>لا توجد رسائل سابقة. ابدأ المحادثة الآن!</p></div>';
                    return;
                }

                let html = '';
                messages.forEach(m => {
                    let isMe = m.sender_type === 'admin';
                    html += `
                        <div class="bubble ${isMe ? 'admin-msg' : 'teacher-msg'}">
                            <div class="msg-content">${m.message}</div>
                            <span class="msg-date">${m.created_at_formatted || ''}</span>
                        </div>`;
                });

                // تحديث فقط إذا كان هناك محتوى جديد
                if(lastContentHash !== html) {
                    feed.innerHTML = html;
                    lastContentHash = html;
                    scrollToBottom();
                }
            })
            .catch(err => console.error("Error fetching messages:", err));
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        const btn = document.getElementById('btn_send');
        if(!input.value.trim()) return;

        let txt = input.value;
        input.value = '';
        btn.disabled = true;

        axios.post('/admin/teachers/send', {
            teacher_id: teacherId,
            message: txt,
            _token: '{{ csrf_token() }}'
        }).then(() => {
            fetchMessages();
        }).catch(err => {
            alert("تعذر إرسال الرسالة، حاول مرة أخرى");
            input.value = txt; // إعادة النص في حال الفشل
        }).finally(() => {
            btn.disabled = false;
        });
    }

    function scrollToBottom() {
        feed.scrollTop = feed.scrollHeight;
    }

    // التشغيل الأولي
    fetchMessages();

    // التحديث الدوري كل 4 ثوانٍ
    setInterval(fetchMessages, 4000);
</script>
@endsection
