@extends('layouts.app')

@section('content')
<style>
    /* تنسيقات عامة للمكان */
    :root {
        --primary-color: #4361ee;
        --bg-light: #f8f9fa;
        --text-dark: #2b2d42;
        --text-muted: #8d99ae;
        --white: #ffffff;
        --border-color: #edf2f4;
        --admin-bubble: #4361ee;
        --teacher-bubble: #e9ecef;
    }

    .chat-wrapper {
        display: flex;
        height: 85vh;
        gap: 20px;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        direction: rtl;
    }

    /* القائمة الجانبية */
    .teachers-sidebar {
        flex: 0 0 300px;
        background: var(--white);
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-header {
        padding: 20px;
        background: var(--primary-color);
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .teachers-list {
        flex: 1;
        overflow-y: auto;
    }

    .teacher-item {
        display: flex;
        align-items: center;
        padding: 15px;
        text-decoration: none;
        color: var(--text-dark);
        border-bottom: 1px solid var(--border-color);
        transition: 0.3s;
    }

    .teacher-item:hover {
        background-color: #f0f3ff;
    }

    .teacher-item.active {
        background-color: #edf2ff;
        border-right: 4px solid var(--primary-color);
    }

    .avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-left: 12px;
        flex-shrink: 0;
    }

    .teacher-info h6 {
        margin: 0;
        font-size: 0.95rem;
    }

    .teacher-info small {
        color: var(--text-muted);
        font-size: 0.8rem;
    }

    /* منطقة المحادثة */
    .chat-main {
        flex: 1;
        background: var(--white);
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }

    .chat-header {
        padding: 15px 25px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
    }

    .messages-body {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        background-color: #fdfdfd;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* فقاعات الرسائل */
    .message-row {
        display: flex;
        width: 100%;
    }

    .msg-admin { justify-content: flex-start; }
    .msg-teacher { justify-content: flex-end; }

    .bubble {
        max-width: 70%;
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 0.95rem;
        position: relative;
        line-height: 1.5;
    }

    .msg-admin .bubble {
        background: var(--admin-bubble);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .msg-teacher .bubble {
        background: var(--teacher-bubble);
        color: var(--text-dark);
        border-bottom-left-radius: 4px;
    }

    .time {
        display: block;
        font-size: 0.7rem;
        margin-top: 5px;
        opacity: 0.8;
    }

    /* منطقة الإدخال */
    .chat-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
    }

    .input-group {
        display: flex;
        gap: 10px;
    }

    .input-group input {
        flex: 1;
        padding: 12px 20px;
        border: 1px solid #ddd;
        border-radius: 25px;
        outline: none;
        transition: 0.3s;
    }

    .input-group input:focus {
        border-color: var(--primary-color);
    }

    .send-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0 25px;
        border-radius: 25px;
        cursor: pointer;
        transition: 0.3s;
    }

    .send-btn:hover {
        background: #304ccf;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
</style>

<div class="chat-wrapper">

    {{-- قائمة المعلمين --}}
    <aside class="teachers-sidebar">
        <div class="sidebar-header">
            المحادثات
        </div>
        <div class="teachers-list">
            @forelse($teachers as $teacher)
                <a href="{{ route('admin.teachers.chat', ['teacher_id' => $teacher->id]) }}"
                   class="teacher-item {{ isset($selectedTeacher) && $selectedTeacher->id == $teacher->id ? 'active' : '' }}">
                    <div class="avatar">
                        {{ mb_substr($teacher->name, 0, 1) }}
                    </div>
                    <div class="teacher-info">
                        <h6>{{ $teacher->name }}</h6>
                        <small>{{ $teacher->email ?? 'معلم معتمد' }}</small>
                    </div>
                </a>
            @empty
                <div style="padding: 20px; text-align: center; color: #888;">لا يوجد معلمون</div>
            @endforelse
        </div>
    </aside>

    {{-- منطقة الشات --}}
    <main class="chat-main">
        @if(isset($selectedTeacher))
            <div class="chat-header">
                <div class="avatar" style="width: 35px; height: 35px; font-size: 0.8rem;">
                    {{ mb_substr($selectedTeacher->name, 0, 1) }}
                </div>
                <div class="teacher-info" style="margin-right: 10px;">
                <h6 style="font-weight: bold;">{{ $selectedTeacher->name }}</h6>

            </div>
            </div>

            <div id="chat-messages-box" class="messages-body">
                @forelse($messages as $msg)
                    <div class="message-row {{ $msg->sender_type === 'admin' ? 'msg-admin' : 'msg-teacher' }}">
                        <div class="bubble">
                            {{ $msg->message }}
                            <span class="time">
                                {{ $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" id="no-messages-text">
                        <p>لا توجد رسائل سابقة. ابدأ المحادثة الآن.</p>
                    </div>
                @endforelse
            </div>

            <div class="chat-footer">
                <form id="send-message-form" class="input-group">
                    @csrf
                    <input type="hidden" name="teacher_id" id="teacher_id" value="{{ $selectedTeacher->id }}">
                    <input type="text" name="message" id="message-input" placeholder="اكتب رسالتك هنا..." autocomplete="off" required>
                    <button type="submit" class="send-btn">إرسال</button>
                </form>
            </div>
        @else
            <div class="empty-state">
                <h4 style="margin-bottom: 10px;">مرحباً بك في نظام المحادثات</h4>
                <p>اختر معلماً من القائمة الجانبية لبدء المراسلة</p>
            </div>
        @endif
    </main>
</div>

@if(isset($selectedTeacher))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatBox = document.getElementById('chat-messages-box');
        const form = document.getElementById('send-message-form');
        const messageInput = document.getElementById('message-input');
        const teacherIdField = document.getElementById('teacher_id');
        const currentTeacherId = teacherIdField ? teacherIdField.value : null;

        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // 1. إرسال الرسالة عبر AJAX
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const messageText = messageInput.value.trim();
                const teacherId = teacherIdField ? teacherIdField.value : null;

                if (!messageText || !teacherId) return;

                fetch("{{ route('admin.teachers.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId,
                        message: messageText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';

                        const noMsgText = document.getElementById('no-messages-text');
                        if (noMsgText) noMsgText.style.display = 'none';

                        const timeNow = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });

                        // إضافة الرسالة فوراً للشات من جهة الأدمن
                        const messageHtml = `
                            <div class="message-row msg-admin">
                                <div class="bubble">
                                    ${escapeHtml(messageText)}
                                    <span class="time">${timeNow}</span>
                                </div>
                            </div>
                        `;
                        chatBox.insertAdjacentHTML('beforeend', messageHtml);
                        chatBox.scrollTop = chatBox.scrollHeight;
                    } else {
                        alert(data.message || 'حدث خطأ أثناء إرسال الرسالة');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }

        // 2. تحديث تلقائي جبار (يحدث الصفحة بالكامل تلقائياً في الخلفية كل 4 ثوانٍ دون أن يشعر المستخدم ليجلب رسائل المعلم الواردة)
        setInterval(function() {
            // تحقق أن المستخدم ليس كاتباً شيئاً حالياً حتى لا يتم مسح حقل الكتابة عليه
            if (document.activeElement === messageInput && messageInput.value.trim() !== '') {
                return;
            }

            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store' // منع تخزين الكاش لضمان جلب أحدث رسالة فوراً
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChatBox = doc.getElementById('chat-messages-box');

                if (newChatBox && chatBox) {
                    // إذا اختلف محتوى صندوق الرسائل (يعني وصلت رسالة جديدة من المعلم)
                    if (chatBox.innerHTML.trim() !== newChatBox.innerHTML.trim()) {
                        const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 50;
                        chatBox.innerHTML = newChatBox.innerHTML;
                        if (isAtBottom) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    }
                }
            }).catch(err => {});
        }, 4000);

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });
</script>
@endif
@endsection
