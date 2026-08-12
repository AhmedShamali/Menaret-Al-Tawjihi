@extends('layouts.app')

@section('content')
<style>
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
        max-width: 1400px;
        margin: 0 auto;
    }

    /* القائمة الجانبية */
    .teachers-sidebar {
        flex: 0 0 350px;
        background: var(--white);
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .sidebar-header {
        padding: 20px;
        background: var(--primary-color);
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        color: var(--primary-color);
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
        padding: 15px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        background: #fff;
    }

    .back-btn {
        display: none; /* يظهر فقط في الجوال */
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-left: 10px;
        cursor: pointer;
    }

    .messages-body {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #fdfdfd;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .bubble {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .msg-admin { justify-content: flex-start; }
    .msg-admin .bubble {
        background: var(--admin-bubble);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .msg-teacher { justify-content: flex-end; }
    .msg-teacher .bubble {
        background: var(--teacher-bubble);
        color: var(--text-dark);
        border-bottom-left-radius: 4px;
    }

    .time {
        display: block;
        font-size: 0.7rem;
        margin-top: 5px;
        opacity: 0.7;
    }

    .chat-footer {
        padding: 15px;
        background: #fff;
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
    }

    .send-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 25px;
        cursor: pointer;
    }

    /* === Media Queries (التجاوب مع الجوال) === */
    @media (max-width: 768px) {
        .chat-wrapper {
            padding: 0;
            height: 90vh; /* زيادة الارتفاع قليلاً في الجوال */
            gap: 0;
        }

        /* إذا تم اختيار معلم: إخفاء القائمة الجانبية وإظهار الشات */
        @isset($selectedTeacher)
            .teachers-sidebar {
                display: none;
            }
            .chat-main {
                display: flex;
            }
            .back-btn {
                display: block;
            }
        @else
            /* إذا لم يتم اختيار معلم: إظهار القائمة وإخفاء الشات الفارغ */
            .teachers-sidebar {
                flex: 1;
                border-radius: 0;
            }
            .chat-main {
                display: none;
            }
        @endisset

        .bubble {
            max-width: 90%;
        }

        .chat-header {
            padding: 10px 15px;
        }
    }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
</style>

<div class="chat-wrapper">

    {{-- قائمة المعلمين --}}
    <aside class="teachers-sidebar">
        <div class="sidebar-header">
            <span>المحادثات</span>
        </div>
        <div class="teachers-list">
            @forelse($teachers as $teacher)
                <a href="{{ route('admin.teachers.chat', ['teacher_id' => $teacher->id]) }}"
                   class="teacher-item {{ isset($selectedTeacher) && $selectedTeacher->id == $teacher->id ? 'active' : '' }}">
                    <div class="avatar">
                        {{ mb_substr($teacher->name, 0, 1) }}
                    </div>
                    <div class="teacher-info">
                        <h6 class="mb-0">{{ $teacher->name }}</h6>
                        <small>{{ $teacher->email ?? 'معلم معتمد' }}</small>
                    </div>
                </a>
            @empty
                <div class="p-4 text-center text-muted">لا يوجد معلمون</div>
            @endforelse
        </div>
    </aside>

    {{-- منطقة الشات --}}
    <main class="chat-main">
        @if(isset($selectedTeacher))
            <div class="chat-header">
                {{-- زر الرجوع للجوال فقط --}}
                <button class="back-btn" onclick="window.location.href='{{ route('admin.teachers.chat') }}'">
                    <i class="fas fa-arrow-right"></i> →
                </button>

                <div class="avatar" style="width: 40px; height: 40px; font-size: 0.9rem;">
                    {{ mb_substr($selectedTeacher->name, 0, 1) }}
                </div>
                <div class="teacher-info mr-3">
                    <h6 class="mb-0" style="font-weight: bold;">{{ $selectedTeacher->name }}</h6>
                    <small class="text-success">نشط الآن</small>
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
                    <div class="empty-state text-center my-auto" id="no-messages-text">
                        <p class="text-muted">لا توجد رسائل سابقة. ابدأ المحادثة الآن.</p>
                    </div>
                @endforelse
            </div>

            <div class="chat-footer">
                <form id="send-message-form" class="input-group">
                    @csrf
                    <input type="hidden" name="teacher_id" id="teacher_id" value="{{ $selectedTeacher->id }}">
                    <input type="text" name="message" id="message-input" placeholder="اكتب رسالتك هنا..." autocomplete="off" required>
                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i> إرسال
                    </button>
                </form>
            </div>
        @else
            <div class="empty-state m-auto text-center">
                <div class="mb-3">
                    <i class="far fa-comments fa-4x text-light"></i>
                </div>
                <h4>مرحباً بك في نظام المحادثات</h4>
                <p class="text-muted">اختر معلماً من القائمة الجانبية لبدء المراسلة</p>
            </div>
        @endif
    </main>
</div>

{{-- سكربت الـ AJAX والتحديث التلقائي يبقى كما هو مع تعديلات طفيفة لضمان السلاسة --}}
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
                                    <span class="time">${timeNow}</span>
                                </div>
                            </div>`;
                        chatBox.insertAdjacentHTML('beforeend', messageHtml);
                        scrollToBottom();
                    }
                });
            });
        }

        // تحديث المحتوى كل 4 ثوانٍ
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
