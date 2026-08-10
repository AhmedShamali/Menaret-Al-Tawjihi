@extends('layouts.app')

@section('title', 'مركز المراسلات الذكي')

@section('content')
<div class="chat-container">

    <!-- القائمة الجانبية للطلاب -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h2>المراسلات 📥</h2>
            <div class="search-box">
                <input type="text" id="search_student" onkeyup="filterStudents()" placeholder="🔍 ابحث عن طالب...">
            </div>
            <div class="students-count">
                إجمالي الطلاب: <strong id="count_badge">{{ count($chats) }}</strong>
            </div>
        </div>

        <div class="student-list" id="student_list">
            @forelse($chats as $student)
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
            <div class="empty-list">لا يوجد طلاب مسجلين حتى الآن</div>
            @endforelse
            <div id="no_results" class="empty-list" style="display: none;">لا يوجد طالب مطابق للبحث</div>
        </div>
    </div>

    <!-- منطقة المحادثة الرئيسية -->
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
                <h3>اختر طالباً لبدء المراسلة</h3>
                <p>يمكنك اختيار أي طالب مسجل من القائمة الجانبية ومراسلته فوراً</p>
            </div>
        </div>

        <div id="input_area" class="input-area" style="display: none;">
            <form id="chatForm" onsubmit="event.preventDefault(); sendReply();">
                <input type="text" id="msg_input" placeholder="اكتب ردك الأكاديمي هنا..." autocomplete="off" required>
                <button type="submit" class="btn-send" id="btn_send">
                    <span>إرسال</span> 🚀
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
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
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid var(--border-color);
        margin: 10px 0;
    }

    .chat-sidebar {
        background: #fdfdfd;
        border-left: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }

    .sidebar-header { padding: 20px; border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
    .sidebar-header h2 { font-size: 1.3rem; font-weight: 800; color: var(--text-dark); margin-bottom: 12px; }
    .search-box input { width: 100%; padding: 10px 16px; border-radius: 12px; border: 1px solid var(--border-color); background: #f1f5f9; outline: none; font-size: 0.88rem; }
    .students-count { font-size: 0.75rem; color: var(--text-muted); margin-top: 10px; }

    .student-list {
        flex: 1;
        overflow-y: auto;
    }

    .chat-user-card { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background 0.2s ease; }
    .chat-user-card:hover { background: #f1f5f9; }
    .chat-user-card.active { background: #eff6ff; border-right: 4px solid var(--primary-color); }
    .avatar { width: 44px; height: 44px; border-radius: 14px; background: #e0e7ff; color: var(--primary-color); display: grid; place-items: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0; }
    .user-info strong { display: block; color: var(--text-dark); font-size: 0.95rem; }
    .user-info p { font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }

    /* تعديل منطقة الشات الرئيسية لحل مشكلة التمرير والسكرول */
    .chat-main {
        display: flex;
        flex-direction: column;
        background: #fff;
        position: relative;
        height: 100%;
        min-height: 0; /* منع الحاوية من التمدد خارج الأبعاد */
        overflow: hidden;
    }

    .chat-header {
        padding: 16px 30px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
        background: #ffffff;
        flex-shrink: 0;
    }

    .header-avatar { background: var(--primary-color); color: #fff; }
    .chat-header h4 { margin: 0; font-weight: 700; color: var(--text-dark); }
    .status-badge { font-size: 0.72rem; color: #10b981; font-weight: 600; }

    .chat-messages {
        flex: 1;
        background: var(--bg-light);
        padding: 30px;
        overflow-y: auto; /* تمكين السكرول العمودي */
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .placeholder-state { text-align: center; margin: auto; color: var(--text-muted); }
    .placeholder-state .icon { font-size: 3.5rem; margin-bottom: 10px; }

    .bubble { max-width: 65%; padding: 12px 18px; border-radius: 18px; font-size: 0.92rem; line-height: 1.5; position: relative; word-break: break-word; }
    .bubble.me { align-self: flex-start; background: var(--primary-color); color: #ffffff; border-bottom-right-radius: 4px; }
    .bubble.them { align-self: flex-end; background: #ffffff; color: var(--text-dark); border-bottom-left-radius: 4px; border: 1px solid var(--border-color); }
    .time { display: block; font-size: 0.65rem; margin-top: 4px; opacity: 0.75; text-align: left; }

    .input-area {
        padding: 20px 30px;
        border-top: 1px solid var(--border-color);
        background: #fff;
        flex-shrink: 0;
    }

    .input-area form { display: flex; gap: 12px; background: var(--bg-light); padding: 8px 12px; border-radius: 16px; border: 1px solid var(--border-color); }
    .input-area input { flex: 1; border: none; background: transparent; outline: none; padding: 0 10px; font-size: 0.95rem; }
    .btn-send { background: var(--primary-color); color: white; border: none; padding: 10px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; }
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
        box.innerHTML = '<div class="placeholder-state"><p>جاري تحميل الرسائل...</p></div>';

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

        // تعديل المسار ليتطابق مع الـ Route الصحيح في الأدمن (/admin/fetch/{id})
        axios.get('/admin/fetch/' + activeStudentId)
            .then(res => {
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                const currentHash = JSON.stringify(messages);
                if (currentHash === lastMessagesHash && !isFirstLoad) {
                    return;
                }
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
                console.error("خطأ جلب الرسائل:", err.response ? err.response.data : err);
            });
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        const message = input.value.trim();
        const sendBtn = document.getElementById('btn_send');

        if(!message || !activeStudentId) return;

        sendBtn.disabled = true;

        // تعديل المسار ليتطابق مع الـ Route الصحيح في الأدمن (/admin/send)
        axios.post('/admin/send', {
            student_id: activeStudentId,
            message: message
        }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(res => {
            input.value = '';
            lastMessagesHash = "";
            fetchMessages(true);
        }).catch(err => {
            console.error("خطأ الإرسال:", err.response ? err.response.data : err);

            let errorMsg = 'تعذر إرسال الرسالة';
            if (err.response && err.response.data && err.response.data.message) {
                errorMsg += ':\n' + err.response.data.message;
            } else if (err.response && err.response.status === 404) {
                errorMsg += ': المسار (/admin/send) غير موجود في routes/web.php';
            } else if (err.response && err.response.status === 419) {
                errorMsg += ': انتهت جلسة CSRF، أعد تحديث الصفحة';
            }

            alert(errorMsg);
        }).finally(() => {
            sendBtn.disabled = false;
        });
    }

    function appendMessage(msg) {
        const box = document.getElementById('chat_messages');
        if(box.querySelector('.placeholder-state')) {
            box.innerHTML = '';
        }

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
        if (box) {
            box.scrollTop = box.scrollHeight;
        }
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

        const noResults = document.getElementById('no_results');
        if(visibleCount === 0 && cards.length > 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
</script>
@endsection
