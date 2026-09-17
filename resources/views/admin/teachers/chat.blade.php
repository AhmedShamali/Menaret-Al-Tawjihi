@extends('layouts.app')

@section('title', __('المحادثات المباشرة مع المعلمين') . ' - ' . __('إدارة المنصة'))

@section('content')
<style>
    .chat-wrapper {
        display: flex;
        height: 84vh;
        gap: 20px;
        padding: 0;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }

    /* القائمة الجانبية للمعلمين */
    .teachers-sidebar {
        flex: 0 0 340px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .sidebar-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #0f172a;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-inline-start: 4px solid var(--ed-primary, #1e3a8a);
    }

    .teachers-list {
        flex: 1;
        overflow-y: auto;
    }

    .teacher-item {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        text-decoration: none;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        transition: 0.15s;
        gap: 12px;
    }

    .teacher-item:hover {
        background-color: #f8fafc;
        color: #1e3a8a;
    }

    .teacher-item.active {
        background-color: #eff6ff;
        border-inline-start: 4px solid #1e3a8a;
        color: #1e3a8a;
    }

    .avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #1e3a8a;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    .teacher-info {
        flex: 1;
        min-width: 0;
    }
    .teacher-info h6 {
        margin: 0 0 2px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .teacher-info small {
        font-size: 0.75rem;
        color: #64748b;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* منطقة المحادثة الرئيسية */
    .chat-main {
        flex: 1;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }

    .chat-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        background: #f8fafc;
        gap: 12px;
    }

    .back-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.1rem;
        color: #1e3a8a;
        cursor: pointer;
        padding: 4px;
    }

    .messages-body {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #fafbfd;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .message-row {
        display: flex;
        width: 100%;
    }

    .bubble {
        max-width: 75%;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 0.9rem;
        line-height: 1.5;
        position: relative;
        word-break: break-word;
    }

    .msg-admin {
        justify-content: flex-end;
    }
    .msg-admin .bubble {
        background: #1e3a8a;
        color: #ffffff;
        border-end-end-radius: 2px;
    }

    .msg-teacher {
        justify-content: flex-start;
    }
    .msg-teacher .bubble {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #0f172a;
        border-end-start-radius: 2px;
    }

    .time {
        display: block;
        font-size: 0.7rem;
        margin-top: 4px;
        opacity: 0.75;
        text-align: end;
    }

    .chat-footer {
        padding: 14px 18px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
    }

    .input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-group input {
        flex: 1;
        padding: 10px 16px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        font-size: 0.88rem;
        background: #f8fafc;
    }
    .input-group input:focus {
        border-color: #1e3a8a;
        background: #ffffff;
    }

    .send-btn {
        background: #1e3a8a;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .send-btn:hover { background: #172554; }

    .empty-state {
        margin: auto;
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        opacity: 0.4;
    }
    .empty-state h4 {
        color: #475569;
        font-size: 1.1rem;
        margin-bottom: 6px;
        font-weight: 700;
    }

    /* Media queries */
    @media (max-width: 768px) {
        .chat-wrapper {
            padding: 0;
            height: calc(100vh - 140px);
            gap: 0;
        }
        @isset($selectedTeacher)
            .teachers-sidebar { display: none; }
            .chat-main { display: flex; }
            .back-btn { display: block; }
        @else
            .teachers-sidebar { flex: 1; border-radius: 0; }
            .chat-main { display: none; }
        @endisset
        .bubble { max-width: 88%; }
    }
</style>

<div class="chat-wrapper">

    {{-- قائمة المعلمين --}}
    <aside class="teachers-sidebar">
        <div class="sidebar-header">
            <span><i class="fa-solid fa-comments"></i> {{ __('المحادثات الأكاديمية') }}</span>
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
                        <small>{{ $teacher->email ?? __('معلم معتمد') }}</small>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <p>{{ __('لا يوجد معلمون مسجلون') }}</p>
                </div>
            @endforelse
        </div>
    </aside>

    {{-- منطقة الشات --}}
    <main class="chat-main">
        @if(isset($selectedTeacher))
            <div class="chat-header">
                <button type="button" class="back-btn" onclick="window.location.href='{{ route('admin.teachers.chat') }}'" title="{{ __('رجوع') }}">
                    <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
                </button>

                <div class="avatar" style="width: 38px; height: 38px; font-size: 0.88rem;">
                    {{ mb_substr($selectedTeacher->name, 0, 1) }}
                </div>
                <div class="teacher-info">
                    <h6 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0f172a;">{{ $selectedTeacher->name }}</h6>
                    <small style="color: #059669; font-weight: 600;"><i class="fa-solid fa-circle" style="font-size: 0.55rem;"></i> {{ __('نشط للمراسلة') }}</small>
                </div>
            </div>

            <div id="chat-messages-box" class="messages-body">
                @forelse($messages as $msg)
                    <div class="message-row {{ $msg->sender_type === 'admin' ? 'msg-admin' : 'msg-teacher' }}">
                        <div class="bubble">
                            {{ $msg->message }}
                            <span class="time font-mono">
                                {{ $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" id="no-messages-text">
                        <i class="fa-regular fa-comment-dots"></i>
                        <h4>{{ __('لا توجد رسائل سابقة') }}</h4>
                        <p>{{ __('ابدأ المحادثة مع المعلم الآن.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="chat-footer">
                <form id="send-message-form" class="input-group">
                    @csrf
                    <input type="hidden" name="teacher_id" id="teacher_id" value="{{ $selectedTeacher->id }}">
                    <input type="text" name="message" id="message-input" placeholder="{{ __('اكتب رسالتك للمعلم هنا...') }}" autocomplete="off" required>
                    <button type="submit" class="send-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('إرسال') }}</span>
                    </button>
                </form>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-comments"></i>
                <h4>{{ __('مرحباً بك في نظام محادثات الكادر التعليمي') }}</h4>
                <p>{{ __('اختر معلماً من القائمة الجانبية لبدء المحادثة المباشرة.') }}</p>
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

        const scrollToBottom = () => {
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        };

        scrollToBottom();

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const messageText = messageInput.value.trim();
                const teacherId = teacherIdField.value;

                if (!messageText) return;

                fetch("{{ route('admin.teachers.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ teacher_id: teacherId, message: messageText })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';
                        const noMsgText = document.getElementById('no-messages-text');
                        if (noMsgText) noMsgText.style.display = 'none';

                        const timeNow = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
                        const messageHtml = `
                            <div class="message-row msg-admin">
                                <div class="bubble">
                                    ${escapeHtml(messageText)}
                                    <span class="time font-mono">${timeNow}</span>
                                </div>
                            </div>`;
                        chatBox.insertAdjacentHTML('beforeend', messageHtml);
                        scrollToBottom();
                    }
                });
            });
        }

        // Auto poll every 4 seconds
        setInterval(function() {
            if (document.activeElement === messageInput && messageInput.value.trim() !== '') return;

            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChatBox = doc.getElementById('chat-messages-box');

                if (newChatBox && chatBox && chatBox.innerHTML.trim() !== newChatBox.innerHTML.trim()) {
                    const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 100;
                    chatBox.innerHTML = newChatBox.innerHTML;
                    if (isAtBottom) scrollToBottom();
                }
            }).catch(err => {});
        }, 4000);

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    });
</script>
@endif
@endsection
