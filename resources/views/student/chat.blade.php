@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

<style>
    .chat-card {
        font-family: 'Tajawal', sans-serif;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        max-width: 900px;
        margin: 20px auto;
        overflow: hidden;
    }

    .chat-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        color: #ffffff;
        padding: 20px 25px;
        border-bottom: 4px solid #d97706;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-body {
        height: 480px;
        overflow-y: auto;
        background-color: #f8fafc;
        padding: 25px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .msg-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 70%;
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
        padding: 12px 18px;
        border-radius: 16px;
        font-size: 0.95rem;
        font-weight: 500;
        line-height: 1.5;
        position: relative;
        word-break: break-word;
    }

    .student .msg-bubble {
        background: #1e3a8a;
        color: #ffffff;
        border-bottom-left-radius: 4px;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
    }

    .teacher .msg-bubble {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }

    .msg-time {
        font-size: 0.72rem;
        margin-top: 4px;
        opacity: 0.75;
    }

    .chat-footer {
        padding: 18px 25px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
    }

    .chat-input-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .chat-input {
        flex: 1;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 12px 18px;
        font-family: 'Tajawal', sans-serif;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .chat-input:focus {
        border-color: #1e3a8a;
    }

    .btn-send {
        background: #1e3a8a;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-family: 'Tajawal', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-send:hover {
        background: #1d4ed8;
    }

    .btn-send:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }
</style>

<div class="container py-4">
    <div class="chat-card">
        <!-- Header -->
        <div class="chat-header">
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800;">محادثة المدرس 👨‍🏫</h3>
                <span style="font-size: 0.82rem; opacity: 0.85;">تواصل مباشرة مع معلّم المادة</span>
            </div>
            <span style="background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">مباشر</span>
        </div>

        <!-- Body / Chat Box -->
        <div class="chat-body" id="chatBox">
            <div style="text-align: center; color: #64748b; margin: auto;" id="loadingState">
                جاري تحميل المحادثة...
            </div>
        </div>

        <!-- Footer / Input -->
        <div class="chat-footer">
            <form id="chatForm" onsubmit="return false;">
                @csrf
                <!-- نرسل الـ teacher_id كـ hidden input -->
                <input type="hidden" id="teacherIdInput" value="{{ $teacher->id ?? 1 }}">

                <div class="chat-input-group">
                    <input type="text" id="messageInput" class="chat-input" placeholder="اكتب رسالتك للمدرس..." autocomplete="off">
                    <button type="button" id="sendBtn" class="btn-send">
                        <span>إرسال</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        // إعدادات الـ AJAX لضمان الأمان والجلسة
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            xhrFields: {
                withCredentials: true
            }
        });

        const teacherId = $('#teacherIdInput').val();

        // 1. جلب الرسائل الخاصة بالمدرس
        function loadMessages() {
            if (!teacherId) return;

            $.ajax({
                url: "/student/teacher-messages/" + teacherId,
                type: "GET",
                success: function (res) {
                    let html = '';
                    if (res.messages && res.messages.length > 0) {
                        res.messages.forEach(function (m) {
                            let isStudent = m.sender_type === 'student';
                            html += `
                                <div class="msg-wrapper ${isStudent ? 'student' : 'teacher'}">
                                    <div class="msg-bubble">
                                        ${m.message}
                                        <div class="msg-time" style="text-align: ${isStudent ? 'left' : 'right'};">
                                            ${m.created_at_formatted}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div style="text-align: center; color: #94a3b8; margin: auto;">لا توجد رسائل بعد. ابدأ المحادثة الآن!</div>';
                    }
                    $('#chatBox').html(html);
                    scrollBottom();
                },
                error: function(err) {
                    if (err.status === 401) {
                        $('#chatBox').html('<div style="text-align: center; color: #ef4444; margin: auto;">انتهت الجلسة، يرجى إعادة تسجيل الدخول.</div>');
                    }
                }
            });
        }

        // 2. إرسال الرسالة إلى المدرس
        function sendMessage() {
            let input = $('#messageInput');
            let text = input.val().trim();

            if (text === '' || !teacherId) return;

            $('#sendBtn').prop('disabled', true);

            $.ajax({
                url: "/student/send-to-teacher",
                type: "POST",
                data: {
                    teacher_id: teacherId,
                    message: text
                },
                success: function (res) {
                    if (res.status === 'success') {
                        input.val('');
                        loadMessages();
                    }
                },
                error: function (err) {
                    if(err.status === 401) {
                        alert('انتهت الجلسة، يرجى إعادة تسجيل الدخول.');
                    } else {
                        alert('تعذر إرسال الرسالة للمدرس، يرجى المحاولة لاحقاً.');
                    }
                },
                complete: function () {
                    $('#sendBtn').prop('disabled', false);
                }
            });
        }

        // أحداث الضغط
        $('#sendBtn').on('click', function (e) {
            e.preventDefault();
            sendMessage();
        });

        $('#messageInput').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        function scrollBottom() {
            let box = document.getElementById('chatBox');
            if (box) box.scrollTop = box.scrollHeight;
        }

        loadMessages();
        setInterval(loadMessages, 4000);
    });
</script>
@endsection
