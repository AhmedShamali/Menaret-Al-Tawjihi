@extends('layouts.app')

@section('title', 'مركز المراسلات الذكي')

@section('content')
<div class="chat-wrapper">
    <div class="chat-container" id="chat_container">

        <!-- القائمة الجانبية للطلاب -->
        <div class="chat-sidebar" id="sidebar_view">
            <div class="sidebar-header">
                <div class="header-top">
                    <h2>المراسلات 📥</h2>
                    <span class="badge-total" id="count_badge">{{ count($chats) }} طالب</span>
                </div>
                <div class="search-box">
                    <i class="search-icon">🔍</i>
                    <input type="text" id="search_student" onkeyup="filterStudents()" placeholder="ابحث عن اسم الطالب أو البريد...">
                </div>
            </div>

            <div class="student-list" id="student_list">
                @forelse($chats as $student)
                @php
                    $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'طالب';
                    $firstLetter = mb_substr($studentName, 0, 1);
                @endphp
                <div onclick="loadChat({{ $student->id }}, '{{ addslashes($studentName) }}')"
                     class="chat-user-card"
                     id="user_{{ $student->id }}"
                     data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '')) }}">

                    <div class="avatar-wrapper">
                        <div class="avatar">{{ $firstLetter }}</div>
                        <span class="online-indicator"></span>
                    </div>
                    <div class="user-info">
                        <div class="user-main">
                            <strong>{{ $studentName }}</strong>
                        </div>
                        <p>{{ $student->email ?? 'طالب مسجل' }}</p>
                    </div>
                    <i class="chevron-icon">←</i>
                </div>
                @empty
                <div class="empty-list">
                    <img src="https://cdn-icons-png.flaticon.com/512/5058/5058436.png" width="50" style="opacity: 0.5">
                    <p>لا يوجد طلاب مسجلين</p>
                </div>
                @endforelse
                <div id="no_results" class="empty-list" style="display: none;">لا يوجد نتائج للبحث</div>
            </div>
        </div>

        <!-- منطقة المحادثة الرئيسية -->
        <div class="chat-main" id="chat_view">
            <!-- الهيدر يظهر فقط عند اختيار طالب -->
            <div id="chat_header" class="chat-header" style="display: none;">
                <button class="back-btn" onclick="toggleMobileView('sidebar')">🔙</button>
                <div class="avatar header-avatar" id="active_avatar">ط</div>
                <div class="active-user-details">
                    <h4 id="active_user_name">اختر طالباً</h4>
                    <span class="status-badge"><span class="dot"></span> جاري المراسلة الآن</span>
                </div>
            </div>

            <!-- منطقة الرسائل -->
            <div id="chat_messages" class="chat-messages">
                <div class="placeholder-state">
                    <div class="welcome-art">💬</div>
                    <h3>مرحباً بك في مركز المراسلات</h3>
                    <p>اختر طالباً من القائمة الجانبية لبدء المحادثة الفورية</p>
                </div>
            </div>

            <!-- منطقة الإدخال -->
            <div id="input_area" class="input-area" style="display: none;">
                <form id="chatForm" onsubmit="event.preventDefault(); sendReply();" class="modern-form">
                    <div class="input-group">
                        <input type="text" id="msg_input" placeholder="اكتب ردك الأكاديمي هنا..." autocomplete="off" required>
                        <button type="submit" class="btn-send" id="btn_send">
                            <span class="send-text">إرسال</span>
                            <span class="send-icon">🚀</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --success: #10b981;
        --bg-main: #f3f4f6;
        --sidebar-bg: #ffffff;
        --text-dark: #1f2937;
        --text-gray: #6b7280;
        --border-color: #f1f5f9;
        --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    /* الحاوية الكبرى */
    .chat-wrapper {
        padding: 20px;
        background: var(--bg-main);
        height: 100vh;
        max-height: 100vh;
        box-sizing: border-box;
    }

    .chat-container {
        display: grid;
        grid-template-columns: 360px 1fr;
        height: calc(100vh - 40px);
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    /* القائمة الجانبية */
    .chat-sidebar {
        background: var(--sidebar-bg);
        border-left: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        z-index: 10;
    }

    .sidebar-header {
        padding: 24px;
        background: #fff;
        border-bottom: 1px solid var(--border-color);
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .header-top h2 { font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin: 0; }

    .badge-total {
        background: var(--primary-light);
        color: var(--primary);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .search-box { position: relative; }
    .search-box input {
        width: 100%;
        padding: 12px 40px 12px 12px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: #f8fafc;
        transition: all 0.3s;
        font-size: 0.9rem;
    }
    .search-box input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
    .search-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5; }

    .student-list { flex: 1; overflow-y: auto; padding: 10px; }

    /* ستايل كرت الطالب */
    .chat-user-card {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

    .chat-user-card:hover { background: #f8fafc; transform: translateY(-1px); }
    .chat-user-card.active { background: var(--primary-light); border-color: rgba(79, 70, 229, 0.1); }

    .avatar-wrapper { position: relative; margin-left: 15px; }
    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary), #818cf8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .online-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: var(--success);
        border: 2px solid #fff;
        border-radius: 50%;
    }

    .user-info { flex: 1; min-width: 0; }
    .user-info strong { display: block; color: var(--text-dark); font-size: 0.95rem; margin-bottom: 2px; }
    .user-info p { font-size: 0.8rem; color: var(--text-gray); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
    .chevron-icon { font-style: normal; opacity: 0; transition: 0.3s; color: var(--primary); }
    .chat-user-card:hover .chevron-icon { opacity: 1; transform: translateX(-5px); }

    /* منطقة الشات */
    .chat-main { display: flex; flex-direction: column; background: #fcfcfd; position: relative; overflow: hidden; }

    .chat-header {
        padding: 15px 25px;
        background: #fff;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
        animation: slideDown 0.3s ease;
    }

    .back-btn { display: none; border: none; background: none; font-size: 1.2rem; cursor: pointer; padding: 5px; margin-right: 5px; }

    .active-user-details h4 { margin: 0; font-size: 1rem; color: var(--text-dark); }
    .status-badge { font-size: 0.75rem; color: var(--success); display: flex; align-items: center; gap: 5px; }
    .status-badge .dot { width: 6px; height: 6px; background: var(--success); border-radius: 50%; animation: pulse 1.5s infinite; }

    .chat-messages {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background-image: radial-gradient(#e5e7eb 0.5px, transparent 0.5px);
        background-size: 20px 20px;
    }

    /* فقاعات الرسائل */
    .bubble {
        max-width: 75%;
        padding: 12px 18px;
        font-size: 0.92rem;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        animation: messageSlide 0.3s ease-out;
    }

    .bubble.me {
        align-self: flex-start;
        background: var(--primary);
        color: #fff;
        border-radius: 4px 18px 18px 18px;
    }

    .bubble.them {
        align-self: flex-end;
        background: #fff;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        border-radius: 18px 4px 18px 18px;
    }

    .time { font-size: 0.65rem; margin-top: 5px; display: block; opacity: 0.7; }

    /* منطقة الكتابة */
    .input-area { padding: 20px; background: #fff; border-top: 1px solid var(--border-color); }
    .modern-form { background: #f8fafc; border-radius: 15px; padding: 5px; border: 1px solid var(--border-color); }
    .input-group { display: flex; align-items: center; gap: 10px; }
    .input-group input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px 15px;
        outline: none;
        font-size: 0.95rem;
    }

    .btn-send {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }
    .btn-send:hover { background: #4338ca; transform: scale(1.02); }
    .btn-send:disabled { opacity: 0.6; cursor: not-allowed; }

    /* الحالات الفارغة */
    .placeholder-state { text-align: center; margin: auto; }
    .welcome-art { font-size: 4rem; margin-bottom: 15px; }
    .empty-list { text-align: center; padding: 40px 20px; color: var(--text-gray); }

    /* --- الاستجابة للموبايل (Responsive) --- */
    @media (max-width: 850px) {
        .chat-wrapper { padding: 0; }
        .chat-container { grid-template-columns: 1fr; border-radius: 0; height: 100vh; }

        /* إخفاء الشات في البداية بالموبايل */
        .chat-main { display: none; }

        /* عند تفعيل الشات */
        .chat-container.active-chat .chat-sidebar { display: none; }
        .chat-container.active-chat .chat-main { display: flex; }

        .back-btn { display: block; }
        .bubble { max-width: 85%; }
        .send-text { display: none; }
    }

    /* تحسين السكرول بار */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* أنيميشن */
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes messageSlide { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let activeStudentId = null;
    let pollInterval = null;
    let lastMessagesHash = "";

    // وظيفة للتبديل بين القائمة والمحادثة في الموبايل
    function toggleMobileView(view) {
        const container = document.getElementById('chat_container');
        if (view === 'chat') {
            container.classList.add('active-chat');
        } else {
            container.classList.remove('active-chat');
            if (pollInterval) clearInterval(pollInterval);
            activeStudentId = null;
        }
    }

    function loadChat(id, name) {
        activeStudentId = id;
        lastMessagesHash = "";

        // للتحويل لعرض المحادثة في الموبايل
        toggleMobileView('chat');

        const box = document.getElementById('chat_messages');
        box.innerHTML = '<div class="placeholder-state"><p>⏳ جاري تحميل المحادثة...</p></div>';

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

        axios.get('/admin/fetch/' + activeStudentId)
            .then(res => {
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                const currentHash = JSON.stringify(messages);
                if (currentHash === lastMessagesHash && !isFirstLoad) return;
                lastMessagesHash = currentHash;

                if(messages.length === 0) {
                    box.innerHTML = '<div class="placeholder-state"><p>لا توجد رسائل سابقة. كن مبادراً!</p></div>';
                    return;
                }

                box.innerHTML = '';
                messages.forEach(m => appendMessage(m));

                if (isFirstLoad || isScrolledToBottom(box)) {
                    setTimeout(scrollToBottom, 50);
                }
            })
            .catch(err => console.error("خطأ جلب الرسائل:", err));
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        const message = input.value.trim();
        const sendBtn = document.getElementById('btn_send');

        if(!message || !activeStudentId) return;

        sendBtn.disabled = true;

        axios.post('/admin/send', {
            student_id: activeStudentId,
            message: message
        }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(res => {
            input.value = '';
            lastMessagesHash = "";
            fetchMessages(true);
        }).catch(err => {
            alert('حدث خطأ أثناء الإرسال');
        }).finally(() => {
            sendBtn.disabled = false;
        });
    }

    function appendMessage(msg) {
        const box = document.getElementById('chat_messages');
        if(box.querySelector('.placeholder-state')) box.innerHTML = '';

        const sender = (msg.sender_type || '').toLowerCase().trim();
        const side = sender === 'admin' ? 'me' : 'them';
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

        document.getElementById('no_results').style.display = (visibleCount === 0) ? 'block' : 'none';
    }
</script>
@endsection
