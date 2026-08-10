@extends('layouts.app')

@section('title', 'محادثات الطلاب - المدرس')

@section('content')
<div class="chat-container">

    <!-- قائمة الطلاب للمدرس -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h2>محادثات الطلاب 📥</h2>
            <div class="search-box">
                <input type="text" id="search_student" onkeyup="filterStudents()" placeholder="🔍 ابحث عن طالب...">
            </div>
            <div class="students-count">
                إجمالي المحادثات: <strong id="count_badge">{{ count($students) }}</strong>
            </div>
        </div>

        <div class="student-list" id="student_list">
            @forelse($students as $student)
            @php
                $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'طالب';
            @endphp
            <div onclick="loadChat({{ $student->id }}, '{{ addslashes($studentName) }}')"
                 class="chat-user-card"
                 id="user_{{ $student->id }}"
                 data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '')) }}">

                <div class="avatar">{{ mb_substr($studentName, 0, 1) }}</div>
                <div class="user-info">
                    <strong>{{ $studentName }}</strong>
                    <p>{{ $student->email ?? 'طالب مسجل' }}</p>
                </div>
            </div>
            @empty
            <div class="empty-list">لا توجد محادثات طلاب حالية</div>
            @endforelse
            <div id="no_results" class="empty-list" style="display: none;">لا يوجد طالب مطابق</div>
        </div>
    </div>

    <!-- منطقة المحادثة -->
    <div class="chat-main">
        <div id="chat_header" class="chat-header" style="display: none;">
            <div class="avatar header-avatar" id="active_avatar">ط</div>
            <div>
                <h4 id="active_user_name">اختر طالباً</h4>
                <span class="status-badge">مُتصل الآن</span>
            </div>
        </div>

        <div id="chat_messages" class="chat-messages">
            <div class="placeholder-state">
                <div class="icon">💬</div>
                <h3>اختر طالباً من القائمة لبدء التواصل</h3>
            </div>
        </div>

        <div id="input_area" class="input-area" style="display: none;">
            <form id="chatForm">
                <input type="text" id="msg_input" placeholder="اكتب ردك الأكاديمي للطالب هنا..." autocomplete="off" required>
                <button type="submit" class="btn-send" id="btn_send">
                    <span>إرسال</span> 🚀
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #0f172a;
        --text-muted: #64748b;
    }

    .chat-container {
        height: calc(100vh - 120px);
        max-height: calc(100vh - 120px);
        display: grid;
        grid-template-columns: 340px 1fr;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        margin: 10px 0;
    }

    .chat-sidebar { background: #fafafa; border-left: 1px solid var(--border-color); display: flex; flex-direction: column; height: 100%; min-height: 0; }
    .sidebar-header { padding: 20px; border-bottom: 1px solid var(--border-color); flex-shrink: 0; background: #ffffff; }
    .sidebar-header h2 { font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin-bottom: 12px; }
    .search-box input { width: 100%; padding: 10px 16px; border-radius: 12px; border: 1px solid var(--border-color); background: #f1f5f9; outline: none; font-size: 0.88rem; transition: 0.2s; }
    .search-box input:focus { border-color: #2563eb; background: #ffffff; }
    .students-count { font-size: 0.78rem; color: var(--text-muted); margin-top: 10px; font-weight: 600; }
    .student-list { flex: 1; overflow-y: auto; }

    .chat-user-card { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s ease; }
    .chat-user-card:hover { background: #f1f5f9; }
    .chat-user-card.active { background: #eff6ff; border-right: 4px solid #2563eb; }

    .avatar { width: 44px; height: 44px; border-radius: 14px; background: #dbeafe; color: #1d4ed8; display: grid; place-items: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0; }
    .user-info strong { display: block; color: var(--text-dark); font-size: 0.92rem; }
    .user-info p { font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 190px; }

    .chat-main { display: flex; flex-direction: column; background: #ffffff; position: relative; height: 100%; min-height: 0; overflow: hidden; }
    .chat-header { padding: 16px 25px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 15px; background: #ffffff; flex-shrink: 0; }
    .header-avatar { background: var(--primary-gradient); color: #ffffff; }
    .chat-header h4 { margin: 0; font-weight: 700; color: var(--text-dark); }
    .status-badge { font-size: 0.72rem; color: #10b981; font-weight: 600; }

    .chat-messages { flex: 1; background: var(--bg-light); padding: 25px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
    .placeholder-state { text-align: center; margin: auto; color: var(--text-muted); }
    .placeholder-state .icon { font-size: 3.5rem; margin-bottom: 10px; }

    .bubble { max-width: 65%; padding: 12px 18px; border-radius: 18px; font-size: 0.92rem; line-height: 1.5; position: relative; word-break: break-word; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }

    .bubble.me { align-self: flex-start; background: var(--primary-gradient); color: #ffffff; border-bottom-right-radius: 4px; }
    .bubble.them { align-self: flex-end; background: #ffffff; color: var(--text-dark); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); }

    .time { display: block; font-size: 0.65rem; margin-top: 4px; opacity: 0.8; text-align: left; }

    .input-area { padding: 16px 25px; border-top: 1px solid var(--border-color); background: #ffffff; flex-shrink: 0; }
    .input-area form { display: flex; gap: 12px; background: var(--bg-light); padding: 6px 10px 6px 16px; border-radius: 16px; border: 1px solid var(--border-color); }
    .input-area input { flex: 1; border: none; background: transparent; outline: none; font-size: 0.92rem; }
    .btn-send { background: var(--primary-gradient); color: white; border: none; padding: 10px 22px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: opacity 0.2s; }
    .btn-send:hover { opacity: 0.9; }
    .empty-list { text-align: center; padding: 30px; color: var(--text-muted); font-size: 0.88rem; }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let activeStudentId = null;
    let pollInterval = null;
    let lastMessagesHash = "";

    function loadChat(id, name) {
        activeStudentId = id;
        lastMessagesHash = "";

        const box = document.getElementById('chat_messages');
        box.innerHTML = '<div class="placeholder-state"><p>جاري تحميل المحادثة...</p></div>';

        document.getElementById('chat_header').style.display = 'flex';
        document.getElementById('input_area').style.display = 'block';
        document.getElementById('active_user_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);

        document.querySelectorAll('.chat-user-card').forEach(c => c.classList.remove('active'));
        const activeCard = document.getElementById('user_' + id);
        if(activeCard) activeCard.classList.add('active');

        fetchMessages(true);

        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3000);
    }

    function fetchMessages(isFirstLoad = false) {
        if (!activeStudentId) return;

        axios.get('/teacher/messages/' + activeStudentId)
            .then(res => {
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                const currentHash = JSON.stringify(messages);
                if (currentHash === lastMessagesHash && !isFirstLoad) return;
                lastMessagesHash = currentHash;

                if(messages.length === 0) {
                    box.innerHTML = '<div class="placeholder-state"><p>لا توجد رسائل سابقة. ابدأ المحادثة الآن!</p></div>';
                    return;
                }

                box.innerHTML = '';
                messages.forEach(m => appendMessage(m));

                if (isFirstLoad || isScrolledToBottom(box)) {
                    setTimeout(scrollToBottom, 50);
                }
            })
            .catch(err => {
                console.error("Fetch Error: ", err);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('chatForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const input = document.getElementById('msg_input');
                const message = input.value.trim();
                const sendBtn = document.getElementById('btn_send');

                if (!message || !activeStudentId) return;

                sendBtn.disabled = true;

                axios.post('/teacher/send-message', {
                    student_id: activeStudentId,
                    message: message
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => {
                    input.value = '';
                    lastMessagesHash = "";
                    fetchMessages(true);
                })
                .catch(err => {
                    console.error("Send Error: ", err);
                    alert('حدث خطأ أثناء الإرسال، يرجى إعادة المحاولة.');
                })
                .finally(() => {
                    sendBtn.disabled = false;
                });
            });
        }
    });

    function appendMessage(msg) {
        const box = document.getElementById('chat_messages');
        if(box.querySelector('.placeholder-state')) box.innerHTML = '';

        const sender = (msg.sender_type || '').toLowerCase().trim();
        const side = sender === 'teacher' ? 'me' : 'them';
        const time = msg.created_at_formatted ? msg.created_at_formatted : '';

        const html = `
            <div class="bubble ${side}">
                <div>${msg.message}</div>
                ${time ? `<span class="time">${time}</span>` : ''}
            </div>
        `;
        box.insertAdjacentHTML('beforeend', html);
    }

    function scrollToBottom() {
        const box = document.getElementById('chat_messages');
        if (box) box.scrollTop = box.scrollHeight;
    }

    function isScrolledToBottom(box) {
        return box.scrollHeight - box.clientHeight <= box.scrollTop + 150;
    }

    function filterStudents() {
        const query = document.getElementById('search_student').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.chat-user-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const searchData = card.getAttribute('data-search');
            if(searchData.includes(query)) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('no_results').style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
    }
</script>
@endsection
