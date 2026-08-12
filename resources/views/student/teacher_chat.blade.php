@extends('layouts.app')

@section('content')
<style>
    /* المتغيرات الأساسية */
    :root {
        --primary-blue: #4361ee;
        --secondary-blue: #3651d1;
        --light-bg: #f1f4f8;
        --teacher-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* الحاوية الرئيسية - ضبط الارتفاع ليتناسب مع الجوال */
    .chat-main-wrapper {
        display: flex;
        flex-direction: column;
        height: 80vh; /* ارتفاع افتراضي */
        height: calc(100dvh - 100px); /* ارتفاع ديناميكي يراعي شريط المتصفح */
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
        margin: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* الهيدر: متجاوب */
    .chat-nav {
        padding: 12px 20px;
        background: #fff;
        border-bottom: 2px solid #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .teacher-meta {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .teacher-icon {
        width: 45px;
        height: 45px;
        background: var(--teacher-gradient);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .status-badge {
        font-size: 10px;
        color: #2dce89;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-badge::before {
        content: '';
        width: 7px;
        height: 7px;
        background: #2dce89;
        border-radius: 50%;
    }

    /* منطقة الرسائل */
    .chat-feed {
        flex: 1;
        padding: 20px; /* تقليل البادينج للجوال */
        background: #fdfdfd;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scrollbar-width: thin;
    }

    .bubble {
        max-width: 85%; /* زيادة العرض في الجوال */
        padding: 10px 16px;
        font-size: 0.92rem;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }

    .student-msg {
        align-self: flex-end;
        background: var(--primary-blue);
        color: white;
        border-radius: 18px 18px 2px 18px;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
    }

    .teacher-msg {
        align-self: flex-start;
        background: #fff;
        color: #2d3748;
        border: 1px solid #e2e8f0;
        border-radius: 18px 18px 18px 2px;
    }

    .msg-date {
        font-size: 9px;
        display: block;
        margin-top: 4px;
        opacity: 0.7;
    }

    /* صندوق الإدخال */
    .chat-input-bar {
        padding: 15px 20px;
        background: #fff;
        border-top: 2px solid #f8f9fa;
        flex-shrink: 0;
    }

    .input-group-custom {
        display: flex;
        align-items: center;
        background: var(--light-bg);
        border-radius: 15px;
        padding: 5px 10px;
        gap: 5px;
    }

    .input-group-custom input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px;
        outline: none;
        font-size: 14px;
        min-width: 0; /* مهم جداً لمنع تمدد الحقل في الجوال */
    }

    .send-trigger {
        background: var(--primary-blue);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
    }

    .close-chat-btn {
        text-decoration: none;
        color: #a0aec0;
        font-size: 12px;
        border: 1px solid #e2e8f0;
        padding: 4px 12px;
        border-radius: 8px;
        white-space: nowrap;
    }

    /* === استعلامات الوسائط (Responsive Queries) === */
    @media (max-width: 576px) {
        .chat-main-wrapper {
            margin: 0; /* إلغاء الهوامش في الجوال */
            border-radius: 0; /* جعل الشات بملء الشاشة */
            height: calc(100dvh - 60px); /* استغلال كامل المساحة */
            border: none;
        }

        .chat-nav {
            padding: 10px 15px;
        }

        .teacher-icon {
            width: 38px;
            height: 38px;
            font-size: 1rem;
        }

        .chat-feed {
            padding: 15px;
        }

        .bubble {
            max-width: 90%;
            font-size: 0.88rem;
        }

        .chat-input-bar {
            padding: 10px 15px;
        }

        .send-trigger {
            padding: 8px 12px;
        }
    }

    /* تحسين السكرول بار */
    .chat-feed::-webkit-scrollbar { width: 4px; }
    .chat-feed::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<div class="container-fluid p-0" dir="rtl">
    <div class="chat-main-wrapper">

        <!-- الهيدر -->
        <div class="chat-nav">
            <div class="teacher-meta">
                <div class="teacher-icon">{{ mb_substr($teacher->name, 0, 1) }}</div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ $teacher->name }}</h6>
                    <div class="status-badge">متاح الآن</div>
                </div>
            </div>
            <a href="{{ route('student.teachers.index') }}" class="close-chat-btn">
                <span class="d-none d-sm-inline">إنهاء المحادثة</span>
                <span class="d-inline d-sm-none">إغلاق</span>
            </a>
        </div>

        <!-- منطقة الرسائل -->
        <div class="chat-feed" id="chat_messages">
            <div id="empty_state" class="text-center my-auto py-5 text-muted">
                <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" width="50" style="opacity: 0.3" class="mb-3">
                <p style="font-size: 0.9rem;">ابدأ بمراسلة معلمك الآن..</p>
            </div>
        </div>

        <!-- شريط الإدخال -->
        <div class="chat-input-bar">
            <div class="input-group-custom">
                <input type="text" id="msg_input" placeholder="اكتب رسالتك..." onkeypress="if(event.key === 'Enter') sendReply()">
                <button class="send-trigger" onclick="sendReply()">إرسال</button>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const teacherId = {{ $teacher->id }};
    const feed = document.getElementById('chat_messages');
    const msgInput = document.getElementById('msg_input');
    let lastContent = "";

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
                            ${escapeHtml(m.message)}
                            <span class="msg-date">${m.created_at_formatted}</span>
                        </div>`;
                });

                if(lastContent !== html) {
                    const isAtBottom = feed.scrollHeight - feed.scrollTop <= feed.clientHeight + 100;
                    feed.innerHTML = html;
                    lastContent = html;
                    if(isAtBottom) {
                        feed.scrollTop = feed.scrollHeight;
                    }
                }
            }).catch(err => console.error("خطأ في جلب الرسائل"));
    }

    function sendReply() {
        const txt = msgInput.value.trim();
        if(!txt) return;

        msgInput.value = '';

        axios.post('/student/teachers/send', {
            teacher_id: teacherId,
            message: txt,
            _token: '{{ csrf_token() }}'
        }).then(() => {
            fetchMessages();
            feed.scrollTop = feed.scrollHeight;
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // تحميل الرسائل عند البدء
    fetchMessages();

    // تحديث كل 4 ثوانٍ
    setInterval(fetchMessages, 4000);
</script>
@endsection
