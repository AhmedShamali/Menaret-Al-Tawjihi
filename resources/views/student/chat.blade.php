@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<style>
    /* المتغيرات الأساسية لسهولة التعديل */
    :root {
        --primary-blue: #1e3a8a;
        --secondary-blue: #1d4ed8;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #0f172a;
        --accent-orange: #d97706;
    }

    .chat-card {
        font-family: 'Tajawal', sans-serif;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        max-width: 900px;
        margin: 10px auto;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        min-height: 480px;
    }

    @media (max-width: 768px) {
        .chat-card {
            margin: 0 !important;
            border-radius: 0 !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            height: 100dvh !important;
            min-height: 100dvh !important;
            box-shadow: none !important;
        }
        .chat-header {
            padding: 10px 14px !important;
        }
        .chat-body {
            padding: 12px 14px !important;
        }
        .chat-footer {
            padding: 8px 12px !important;
            padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px)) !important;
        }
        .chat-input {
            font-size: 16px !important;
        }
    }

    .chat-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #0f172a 100%);
        color: #ffffff;
        padding: 15px 20px;
        border-bottom: 4px solid var(--accent-orange);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0; /* منع الهيدر من التقلص */
    }

    .chat-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .chat-body {
        flex: 1; /* يأخذ كل المساحة المتاحة */
        overflow-y: auto;
        background-color: var(--bg-light);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        scrollbar-width: thin;
    }

    .msg-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 85%; /* زيادة العرض قليلاً للجوال */
    }

    /* رسائل الطالب (يمين) */
    .msg-wrapper.student {
        align-self: flex-end;
        align-items: flex-end;
    }

    /* رسائل المدرس (يسار) */
    .msg-wrapper.teacher {
        align-self: flex-start;
        align-items: flex-start;
    }

    .msg-bubble {
        padding: 10px 15px;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.5;
        position: relative;
        word-break: break-word;
    }

    .student .msg-bubble {
        background: var(--primary-blue);
        color: #ffffff;
        border-bottom-left-radius: 2px;
    }

    .teacher .msg-bubble {
        background: #ffffff;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        border-bottom-right-radius: 2px;
    }

    .msg-time {
        font-size: 0.65rem;
        margin-top: 4px;
        opacity: 0.7;
    }

    .chat-footer {
        padding: 15px;
        background: #ffffff;
        border-top: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .chat-input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chat-input {
        flex: 1;
        border: 1.5px solid #cbd5e1;
        border-radius: 25px; /* شكل بيضاوي مريح للعين */
        padding: 10px 18px;
        font-family: 'Tajawal', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: 0.2s;
    }

    .chat-input:focus {
        border-color: var(--primary-blue);
        background: #fff;
    }

    .btn-send {
        background: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 50%; /* شكل دائري في الجوال */
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
        flex-shrink: 0;
    }

    /* إخفاء نص "إرسال" في الجوال وإظهار الأيقونة فقط */
    .btn-send span {
        display: none;
    }

    .btn-send:hover {
        background: var(--secondary-blue);
        transform: scale(1.05);
    }

    /* تحسينات للشاشات الكبيرة */
    @media (min-width: 768px) {
        .chat-card {
            margin: 20px auto;
            height: 600px; /* ارتفاع ثابت للديسك توب */
        }
        .chat-header h3 { font-size: 1.25rem; }
        .msg-wrapper { max-width: 70%; }
        .msg-bubble { font-size: 0.95rem; }

        .btn-send {
            border-radius: 12px;
            width: auto;
            height: auto;
            padding: 10px 20px;
        }
        .btn-send span { display: inline; margin-left: 8px; }
    }

    /* تحسين شكل السكرول بار */
    .chat-body::-webkit-scrollbar { width: 4px; }
    .chat-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="container-fluid px-2 px-md-4">
    <div class="chat-card">
        <!-- Header -->
        <div class="chat-header">
            <div>
                <h3>محادثة المدرس 👨‍🏫</h3>
                <span style="font-size: 0.75rem; opacity: 0.85;">{{ __('تواصل مباشرة مع معلّم المادة') }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="d-none d-sm-inline" style="background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;">{{ __('مباشر') }}</span>
                <a href="javascript:history.back()" style="color: white; text-decoration: none; font-size: 1.2rem;">✕</a>
            </div>
        </div>

        <!-- Body / Chat Box -->
        <div class="chat-body" id="chatBox">
            <div id="loadingState" style="text-align: center; color: #64748b; margin: auto;">
                <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                <div>{{ __('جاري تحميل المحادثة...') }}</div>
            </div>
        </div>

        <!-- Footer / Input -->
        <div class="chat-footer">
            <form id="chatForm" onsubmit="event.preventDefault(); sendMessage();">
                @csrf
                <input type="hidden" id="teacherIdInput" value="{{ $teacher->id ?? 1 }}">

                <div class="chat-input-group">
                    <input type="text" id="messageInput" class="chat-input" placeholder="{{ __('اكتب رسالتك...') }}" autocomplete="off">
                    <button type="submit" id="sendBtn" class="btn-send">
                        <span>{{ __('إرسال') }}</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const teacherInput = document.getElementById('teacherIdInput');
        const teacherId = teacherInput ? teacherInput.value : '';
        const chatBox = document.getElementById('chatBox');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const csrfToken = document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function loadMessages() {
            if (!teacherId || !chatBox) return;

            fetch("/student/teacher-messages/" + teacherId, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(res => {
                let html = '';
                if (res.messages && res.messages.length > 0) {
                    res.messages.forEach(function(m) {
                        const isStudent = m.sender_type === 'student';
                        html += `
                            <div class="msg-wrapper ${isStudent ? 'student' : 'teacher'}">
                                <div class="msg-bubble">
                                    ${escapeHtml(m.message)}
                                    <div class="msg-time">
                                        ${m.created_at_formatted || ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div style="text-align: center; color: #94a3b8; margin: auto;">ابدأ المحادثة الآن! 👋</div>';
                }

                if (chatBox.innerHTML !== html) {
                    const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.offsetHeight + 60;
                    chatBox.innerHTML = html;
                    if (isAtBottom) scrollBottom();
                }
            })
            .catch(err => console.error('Error loading messages:', err));
        }

        function sendMessage() {
            if (!messageInput) return;
            const text = messageInput.value.trim();
            if (text === '' || !teacherId) return;

            if (sendBtn) sendBtn.disabled = true;

            fetch("/student/send-to-teacher", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ teacher_id: teacherId, message: text })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    messageInput.value = '';
                    loadMessages();
                    scrollBottom();
                }
            })
            .catch(err => console.error('Error sending message:', err))
            .finally(() => {
                if (sendBtn) sendBtn.disabled = false;
                messageInput.focus();
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML.replace(/\n/g, '<br>');
        }

        function scrollBottom() {
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', sendMessage);
        }

        if (messageInput) {
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        loadMessages();
        setInterval(loadMessages, 5000);
    });
</script>
@endsection
