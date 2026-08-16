@extends('layouts.app')

@section('content')
<style>
    /* تحسينات عامة وتنسيق الصفحة */
    :root {
        --chat-primary: #0084ff;
        --chat-bg: #f0f2f5;
        --sidebar-width: 320px;
        --text-main: #1c1e21;
        --text-muted: #65676b;
    }

    .support-container {
        height: calc(100vh - 120px);
        margin: 20px;
        background: #fff;
        border-radius: 12px;
        display: flex;
        overflow: hidden;
        box-shadow: 0 12px 28px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    /* القائمة الجانبية (المشرفين) */
    .chat-sidebar {
        width: var(--sidebar-width);
        border-left: 1px solid #e5e5e5;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #e5e5e5;
    }

    .sidebar-header h4 {
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 15px;
    }

    .search-box {
        background: #f0f2f5;
        border-radius: 20px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
    }

    .search-box input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        font-size: 14px;
    }

    .admin-list {
        flex: 1;
        overflow-y: auto;
    }

    .admin-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        cursor: pointer;
        transition: 0.2s;
        border-bottom: 1px solid #f5f5f5;
    }

    .admin-item:hover { background: #f5f6f7; }
    .admin-item.active { background: #e7f3ff; }

    .admin-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--chat-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-left: 12px;
        font-size: 18px;
        flex-shrink: 0;
    }

    .admin-info h6 { margin: 0; font-weight: 600; color: var(--text-main); }
    .admin-info p { margin: 0; font-size: 12px; color: var(--text-muted); }

    /* منطقة الدردشة الرئيسية */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .chat-header {
        padding: 12px 20px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .messages-area {
        flex: 1;
        padding: 20px;
        background: #fff;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* فقاعات الرسائل */
    .msg {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 15px;
        line-height: 1.4;
        position: relative;
    }

    .msg-student {
        align-self: flex-end;
        background: var(--chat-primary);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-admin {
        align-self: flex-start;
        background: #e4e6eb;
        color: #050505;
        border-bottom-left-radius: 4px;
    }

    .msg-time {
        font-size: 10px;
        display: block;
        margin-top: 4px;
        opacity: 0.7;
    }

    /* صندوق الإرسال */
    .input-area {
        padding: 15px 20px;
        border-top: 1px solid #e5e5e5;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-area input {
        flex: 1;
        background: #f0f2f5;
        border: none;
        padding: 10px 18px;
        border-radius: 20px;
        outline: none;
    }

    .send-btn {
        background: transparent;
        border: none;
        color: var(--chat-primary);
        font-weight: bold;
        cursor: pointer;
        font-size: 16px;
    }

    /* شاشة الترحيب */
    .welcome-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }
</style>

<div class="container-fluid py-3" dir="rtl">
    <div class="support-container">

        <!-- القائمة الجانبية -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h4>الدعم الفني</h4>
                <div class="search-box">
                    <input type="text" id="admin_search" placeholder="بحث عن مشرف...">
                </div>
            </div>

            <div class="admin-list" id="support-list">
                @isset($support)
                    @forelse($support as $index => $admin)
                    <div class="admin-item {{ $index === 0 ? 'active' : '' }}"
                         id="admin_card_{{ $admin->id }}"
                         onclick="loadSupportChat({{ $admin->id }}, '{{ $admin->name }}')">
                        <div class="admin-avatar">{{ mb_substr($admin->name, 0, 1) }}</div>
                        <div class="admin-info">
                            <h6>{{ $admin->name }}</h6>
                            <p>متصل الآن</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center p-3 text-muted">لا يوجد مشرفين متاحين</p>
                    @endforelse
                @endisset
            </div>
        </div>

        <!-- منطقة الدردشة -->
        <div class="chat-main">
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="admin-avatar" style="width: 35px; height: 35px; font-size: 14px;" id="active_avatar">
                        {{ isset($support) && $support->count() > 0 ? mb_substr($support->first()->name, 0, 1) : '🛡️' }}
                    </div>
                    <div class="me-2" style="margin-right: 12px;">
                        <h6 class="mb-0 fw-bold" id="active_name">
                            {{ isset($support) && $support->count() > 0 ? $support->first()->name : 'اختر مشرفاً' }}
                        </h6>
                        <small class="text-success" style="font-size: 11px;">نشط الآن</small>
                    </div>
                </div>
                <span class="badge bg-light text-dark border">{{ $studentStage ?? 'طالب' }}</span>
            </div>

            <div class="messages-area" id="chat_messages">
                <div class="welcome-screen">
                    <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" width="80" style="opacity: 0.2;">
                    <p class="mt-3">اختر مشرفاً لبدء المحادثة</p>
                </div>
            </div>

            <div class="input-area">
                <input type="text" id="msg_input" placeholder="اكتب رسالتك هنا..." autocomplete="off">
                <button class="send-btn" onclick="sendSupportMessage()">إرسال</button>
            </div>
        </div>

    </div>
</div>

<script>
    let activeAdminId = {{ isset($support) && $support->count() > 0 ? $support->first()->id : 'null' }};

    function loadSupportChat(id, name) {
        activeAdminId = id;
        document.getElementById('active_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);

        // تمييز العنصر المختار في القائمة الجانبية
        document.querySelectorAll('.admin-item').forEach(el => el.classList.remove('active'));
        let selectedCard = document.getElementById('admin_card_' + id);
        if(selectedCard) {
            selectedCard.classList.add('active');
        }

        fetchMessages();
    }

    function fetchMessages() {
        if (!activeAdminId) return;
        const container = document.getElementById('chat_messages');

        fetch("{{ url('student/support/fetch') }}/" + activeAdminId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.messages) {
                    let html = '';
                    if(data.messages.length === 0) {
                        html = `<div class="welcome-screen">
                                    <p class="text-muted">لا توجد رسائل سابقة، ابدأ المحادثة الآن!</p>
                                </div>`;
                    } else {
                        data.messages.forEach(msg => {
                            let isMe = (msg.sender_type && msg.sender_type.toLowerCase() === 'student');
                            html += `
                                <div class="msg ${isMe ? 'msg-student' : 'msg-admin'}">
                                    ${msg.message}
                                    <span class="msg-time">${msg.created_at_formatted || ''}</span>
                                </div>`;
                        });
                    }
                    container.innerHTML = html;
                    container.scrollTop = container.scrollHeight;
                }
            })
            .catch(err => console.error("Error fetching messages:", err));
    }

    function sendSupportMessage() {
        const input = document.getElementById('msg_input');
        if (!input.value.trim() || !activeAdminId) return;

        let messageText = input.value;
        input.value = '';

        fetch("{{ route('student.support.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: messageText, admin_id: activeAdminId })
        })
        .then(res => res.json())
        .then(data => {
            fetchMessages(); // تحديث الرسائل فوراً بعد الإرسال
        })
        .catch(err => console.error("Error sending message:", err));
    }

    // بحث المشرفين في القائمة الجانبية
    document.getElementById('admin_search').addEventListener('input', function(e) {
        let term = e.target.value.toLowerCase();
        document.querySelectorAll('.admin-item').forEach(item => {
            let name = item.querySelector('h6').innerText.toLowerCase();
            item.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });

    // إرسال عند ضغط Enter
    document.getElementById('msg_input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendSupportMessage();
    });

    // تحديث تلقائي كل 5 ثواني للرسائل الجديدة
    setInterval(() => {
        if (activeAdminId) fetchMessages();
    }, 5000);

    // تشغيل جلب الرسائل أول ما تحمّل الصفحة بالكامل للمشرف الافتراضي الأول
    document.addEventListener("DOMContentLoaded", function() {
        if (activeAdminId) {
            fetchMessages();
        }
    });
</script>
@endsection