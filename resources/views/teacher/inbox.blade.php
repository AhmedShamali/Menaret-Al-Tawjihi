@extends('layouts.app')

@section('title', 'مركز المحادثات والتواصل مع الطلاب 📥')

@section('content')
<div class="teacher-chat-wrapper" dir="rtl">
    <div class="telegram-inbox-grid">

        <!-- القائمة الجانبية للطلاب (WhatsApp/Telegram Sidebar) -->
        <div class="chat-sidebar-pane">
            <div class="sidebar-top-bar">
                <div class="sidebar-title-row">
                    <h2 class="sidebar-headline"><i class="fa-solid fa-comments text-primary"></i> محادثات الطلاب</h2>
                    <span class="badge-total-students" id="count_badge">{{ count($students) }} محادثة</span>
                </div>
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="search_student" onkeyup="filterStudents()" placeholder="ابحث باسم الطالب أو الإيميل..." autocomplete="off">
                </div>
            </div>

            <!-- قائمة كروت الطلاب -->
            <div class="students-scroll-list" id="student_list">
                @forelse($students as $student)
                @php
                    $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'طالب توجيهي';
                    $stageName = $student->stage->name_ar ?? 'فرع الثانوية العامة';
                @endphp
                <div onclick="loadChat({{ $student->id }}, '{{ addslashes($studentName) }}', '{{ addslashes($stageName) }}')"
                     class="student-thread-card"
                     id="user_{{ $student->id }}"
                     data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '') . ' ' . $stageName) }}">

                    <div class="student-avatar-box">
                        {{ mb_substr($studentName, 0, 1) }}
                    </div>
                    <div class="student-thread-meta">
                        <div class="thread-top">
                            <strong class="student-name-text">{{ $studentName }}</strong>
                            <span class="thread-time-tag">طالب</span>
                        </div>
                        <div class="thread-bottom">
                            <span class="student-stage-subtext">{{ $stageName }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-threads-state">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-muted"></i>
                    <p>لا توجد محادثات طلاب مسجلة حالياً</p>
                </div>
                @endforelse
                <div id="no_results" class="empty-threads-state" style="display: none;">
                    <i class="fa-solid fa-user-slash fa-2x mb-2 text-muted"></i>
                    <p>لم يتم العثور على طالب مطابق للبحث</p>
                </div>
            </div>
        </div>

        <!-- ساحة المحادثة الرئيسية (Main Chat Pane) -->
        <div class="chat-viewport-pane">

            <!-- هيدر المحادثة النشطة -->
            <div id="chat_header" class="active-chat-header" style="display: none;">
                <div class="header-student-profile">
                    <div class="header-avatar-circle" id="active_avatar">ط</div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 id="active_user_name" class="active-student-title">اختر طالباً</h3>
                            <span id="active_stage_tag" class="badge-stage-tag">توجيهي</span>
                        </div>
                        <span class="student-online-status">
                            <span class="status-green-dot"></span> متصل في المنصة
                        </span>
                    </div>
                </div>

                <div class="chat-header-actions">
                    <button type="button" class="action-circle-btn" onclick="scrollToBottom()" title="الانتقال لآخر رسالة">
                        <i class="fa-solid fa-arrow-down"></i>
                    </button>
                </div>
            </div>

            <!-- كبسولات الردود الأكاديمية السريعة للمعلم (Quick Teacher Feedback) -->
            <div id="quick_teacher_prompts" class="teacher-canned-prompts-bar" style="display: none;">
                <span class="canned-label"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i> ردود سريعة:</span>
                <div class="canned-scroll-lane">
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('أحسنت يا بطل، إجابتك نموذجية وصحيحة 100%! 👏')">
                        إجابة نموذجية 👏
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('يرجى مراجعة المعطيات وتطبيق القانون الخاص بالدرس خطوة بخطوة ✍️')">
                        مراجعة القانون والخطوات ✍️
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('هذا السؤال من الأسئلة الهامة جداً والمتكررة وزارياً 🎯')">
                        سؤال وزاري هام 🎯
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('سأقوم بشرح هذه النقطة بالتفصيل في الحصة القادمة إن شاء الله ⏰')">
                        شرح في الحصة القادمة ⏰
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('وفقك الله ونفع بك، دوماً نحو الـ 99%! 🌹')">
                        دعاء وتشجيع 🌹
                    </button>
                </div>
            </div>

            <!-- شريط الرسائل -->
            <div id="chat_messages" class="messages-flow-canvas">
                <div class="no-selection-placeholder">
                    <div class="placeholder-icon-circle">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3>مرحباً بك في مركز التواصل الأكاديمي</h3>
                    <p>اختر أحد الطلاب من القائمة الجانبية لاستعراض استفساراته والرد عليه فورياً.</p>
                </div>
            </div>

            <!-- شريط إدخال الرد -->
            <div id="input_area" class="teacher-composer-area" style="display: none;">
                <form id="chatForm" onsubmit="return false;">
                    <div class="composer-flex-container">
                        <input type="text" id="msg_input" class="teacher-msg-input" placeholder="اكتب ردك الأكاديمي والتوجيهي للطالب هنا..." autocomplete="off">
                        <button type="button" class="btn-send-reply" id="btn_send" onclick="sendTeacherReply()">
                            <span>إرسال الرد</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
:root {
    --side-width: 360px;
    --app-height: calc(100vh - 110px);
}

.teacher-chat-wrapper {
    max-width: 1300px;
    margin: 1rem auto;
    padding: 0 1rem;
}

.telegram-inbox-grid {
    height: var(--app-height);
    min-height: 560px;
    display: grid;
    grid-template-columns: var(--side-width) 1fr;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

/* القائمة الجانبية */
.chat-sidebar-pane {
    background: #f8fafc;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
}

body.dark-theme .chat-sidebar-pane {
    background: #0f172a;
    border-left-color: #1e293b;
}

.sidebar-top-bar {
    padding: 16px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}

body.dark-theme .sidebar-top-bar {
    background: #0f172a;
    border-bottom-color: #1e293b;
}

.sidebar-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.sidebar-headline {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

body.dark-theme .sidebar-headline { color: #f8fafc; }

.badge-total-students {
    background: #eff6ff;
    color: #0284c7;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}

.search-input-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.9rem;
}

.search-input-wrapper input {
    width: 100%;
    padding: 9px 38px 9px 14px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    font-size: 0.85rem;
    outline: none;
    transition: 0.2s;
}

.search-input-wrapper input:focus {
    background: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
}

.students-scroll-list {
    flex: 1;
    overflow-y: auto;
}

.student-thread-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: all 0.15s ease;
}

body.dark-theme .student-thread-card {
    border-bottom-color: #1e293b;
}

.student-thread-card:hover {
    background: #f1f5f9;
}

body.dark-theme .student-thread-card:hover {
    background: #1e293b;
}

.student-thread-card.active {
    background: #f0f9ff;
    border-right: 4px solid #0284c7;
}

body.dark-theme .student-thread-card.active {
    background: #1e293b;
    border-right-color: #38bdf8;
}

.student-avatar-box {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: white;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.student-thread-meta {
    flex: 1;
    min-width: 0;
}

.thread-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2px;
}

.student-name-text {
    color: #0f172a;
    font-size: 0.92rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

body.dark-theme .student-name-text { color: #f8fafc; }

.thread-time-tag {
    font-size: 0.7rem;
    color: #94a3b8;
}

.student-stage-subtext {
    font-size: 0.75rem;
    color: #64748b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* الساحة الرئيسية للمحادثة */
.chat-viewport-pane {
    display: flex;
    flex-direction: column;
    background: #f8fafc;
    height: 100%;
    position: relative;
    overflow: hidden;
}

body.dark-theme .chat-viewport-pane {
    background: #0b141a;
}

.active-chat-header {
    padding: 12px 24px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

body.dark-theme .active-chat-header {
    background: #0f172a;
    border-bottom-color: #1e293b;
}

.header-student-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}

.active-student-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

body.dark-theme .active-student-title { color: #f8fafc; }

.badge-stage-tag {
    background: #eff6ff;
    color: #0284c7;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

.student-online-status {
    font-size: 0.75rem;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
}

.status-green-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
}

.action-circle-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.action-circle-btn:hover {
    color: #0284c7;
    background: #f0f9ff;
}

/* شريط الردود السريعة للمعلم */
.teacher-canned-prompts-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 8px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

body.dark-theme .teacher-canned-prompts-bar {
    background: #0f172a;
    border-bottom-color: #1e293b;
}

.canned-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    white-space: nowrap;
}

.canned-scroll-lane {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
}

.canned-scroll-lane::-webkit-scrollbar { display: none; }

.canned-pill {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: 0.2s;
}

.canned-pill:hover {
    background: #0284c7;
    color: white;
    border-color: #0284c7;
}

/* ساحة الرسائل */
.messages-flow-canvas {
    flex: 1;
    padding: 20px 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.no-selection-placeholder {
    margin: auto;
    text-align: center;
    color: #64748b;
    max-width: 380px;
}

.placeholder-icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

.bubble-item {
    max-width: 72%;
    padding: 10px 16px;
    border-radius: 18px;
    font-size: 0.92rem;
    line-height: 1.5;
    word-break: break-word;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    animation: fadeInMsg 0.2s ease-out;
}

@keyframes fadeInMsg {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.bubble-item.teacher-sent {
    align-self: flex-start;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.bubble-item.student-incoming {
    align-self: flex-end;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}

body.dark-theme .bubble-item.student-incoming {
    background: #1e293b;
    color: #f8fafc;
    border-color: #334155;
}

.msg-time-tag {
    display: block;
    font-size: 0.68rem;
    margin-top: 4px;
    text-align: left;
    opacity: 0.8;
}

/* صندوق الإدخال */
.teacher-composer-area {
    padding: 12px 20px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

body.dark-theme .teacher-composer-area {
    background: #0f172a;
    border-top-color: #1e293b;
}

.composer-flex-container {
    display: flex;
    gap: 10px;
    align-items: center;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 5px 12px;
}

body.dark-theme .composer-flex-container {
    background: #1e293b;
    border-color: #334155;
}

.teacher-msg-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.95rem;
    color: #0f172a;
}

body.dark-theme .teacher-msg-input { color: #f8fafc; }

.btn-send-reply {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: white;
    border: none;
    padding: 9px 20px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
}

.btn-send-reply:hover {
    opacity: 0.92;
    transform: translateY(-1px);
}

.empty-threads-state {
    padding: 2.5rem 1rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.88rem;
}

@media (max-width: 820px) {
    .telegram-inbox-grid {
        grid-template-columns: 1fr;
    }
    .chat-sidebar-pane {
        display: block;
    }
}
</style>

<script>
    let activeStudentId = null;
    let pollInterval = null;
    let lastHash = "";

    function loadChat(id, name, stage) {
        activeStudentId = id;
        lastHash = "";

        const box = document.getElementById('chat_messages');
        box.innerHTML = `
            <div class="text-center my-auto py-5 text-muted">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                <p class="mt-2" style="font-size: 0.85rem;">جاري مزامنة أسئلة الطالب...</p>
            </div>`;

        document.getElementById('chat_header').style.display = 'flex';
        document.getElementById('quick_teacher_prompts').style.display = 'flex';
        document.getElementById('input_area').style.display = 'block';

        document.getElementById('active_user_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);
        if(stage) {
            document.getElementById('active_stage_tag').innerText = stage;
        }

        document.querySelectorAll('.student-thread-card').forEach(c => c.classList.remove('active'));
        const card = document.getElementById('user_' + id);
        if(card) card.classList.add('active');

        fetchMessages(true);

        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3000);
    }

    function fetchMessages(isFirst = false) {
        if (!activeStudentId) return;

        axios.get('/teacher/messages/' + activeStudentId)
            .then(res => {
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                const currentHash = JSON.stringify(messages);
                if (currentHash === lastHash && !isFirst) return;
                lastHash = currentHash;

                if (messages.length === 0) {
                    box.innerHTML = `
                        <div class="no-selection-placeholder">
                            <div class="placeholder-icon-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                            <h3>لا توجد رسائل سابقة مع الطالب</h3>
                            <p>بإمكانك إرسال إرشادات أو متابعة أكاديمية للطالب الآن.</p>
                        </div>`;
                    return;
                }

                box.innerHTML = '';
                messages.forEach(m => {
                    const sender = (m.sender_type || '').toLowerCase().trim();
                    const isMe = (sender === 'teacher');
                    const time = m.created_at_formatted || '';

                    const item = document.createElement('div');
                    item.className = `bubble-item ${isMe ? 'teacher-sent' : 'student-incoming'}`;
                    item.innerHTML = `
                        <div>${escapeHtml(m.message)}</div>
                        ${time ? `<span class="msg-time-tag">${time} ${isMe ? '✓✓' : ''}</span>` : ''}
                    `;
                    box.appendChild(item);
                });

                if (isFirst || isScrolledNearBottom(box)) {
                    scrollToBottom();
                }
            })
            .catch(err => console.error("Fetch Messages Error: ", err));
    }

    function sendTeacherReply() {
        const input = document.getElementById('msg_input');
        const text = input.value.trim();
        const sendBtn = document.getElementById('btn_send');

        if (!text || !activeStudentId) return;

        sendBtn.disabled = true;
        sendBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> إرسال...`;

        axios.post('/teacher/send-message', {
            student_id: activeStudentId,
            message: text
        }, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(res => {
            input.value = '';
            lastHash = "";
            fetchMessages(true);
        })
        .catch(err => {
            alert('حدث خطأ أثناء إرسال الرد، يرجى المحاولة ثانية.');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<span>إرسال الرد</span> <i class="fa-solid fa-paper-plane"></i>`;
            input.focus();
        });
    }

    function insertTeacherCanned(text) {
        const input = document.getElementById('msg_input');
        input.value = text;
        input.focus();
    }

    document.getElementById('msg_input')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendTeacherReply();
        }
    });

    function scrollToBottom() {
        const box = document.getElementById('chat_messages');
        if (box) box.scrollTop = box.scrollHeight;
    }

    function isScrolledNearBottom(box) {
        return box.scrollHeight - box.clientHeight <= box.scrollTop + 140;
    }

    function filterStudents() {
        const query = document.getElementById('search_student').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.student-thread-card');
        let count = 0;

        cards.forEach(c => {
            const data = c.getAttribute('data-search') || '';
            if (data.includes(query)) {
                c.style.display = 'flex';
                count++;
            } else {
                c.style.display = 'none';
            }
        });

        document.getElementById('no_results').style.display = (count === 0 && cards.length > 0) ? 'block' : 'none';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
</script>
@endsection
