@extends('layouts.app')

@section('title', __('Student Inquiries & Chat Center') . ' | ' . config('app.name'))

@section('content')
<div class="teacher-chat-wrapper">
    <div class="telegram-inbox-grid">

        <!-- القائمة الجانبية للطلاب (Sidebar) -->
        <div class="chat-sidebar-pane">
            <div class="sidebar-top-bar">
                <div class="sidebar-title-row">
                    <h2 class="sidebar-headline"><i class="fa-solid fa-comments" style="color: #1e3a8a;"></i> {{ __('Student Chats') }}</h2>
                    <span class="badge-total-students" id="count_badge">{{ count($students) }} {{ __('conversations') }}</span>
                </div>
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="search_student" onkeyup="filterStudents()" placeholder="{{ __('Search by student name or email...') }}" autocomplete="off">
                </div>
            </div>

            <!-- قائمة كروت الطلاب -->
            <div class="students-scroll-list" id="student_list">
                @forelse($students as $student)
                @php
                    $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: __('Student');
                    $stageName = $student->stage->name_ar ?? __('High School Branch');
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
                            <span class="thread-time-tag">{{ __('Student') }}</span>
                        </div>
                        <div class="thread-bottom">
                            <span class="student-stage-subtext">{{ $stageName }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-threads-state">
                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-muted"></i>
                    <p>{{ __('No student chats registered currently') }}</p>
                </div>
                @endforelse
                <div id="no_results" class="empty-threads-state" style="display: none;">
                    <i class="fa-solid fa-user-slash fa-2x mb-2 text-muted"></i>
                    <p>{{ __('No matching students found') }}</p>
                </div>
            </div>
        </div>

        <!-- ساحة المحادثة الرئيسية (Main Chat Pane) -->
        <div class="chat-viewport-pane">

            <!-- هيدر المحادثة النشطة -->
            <div id="chat_header" class="active-chat-header" style="display: none;">
                <div class="header-student-profile">
                    <div class="header-avatar-circle" id="active_avatar">S</div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h3 id="active_user_name" class="active-student-title">{{ __('Select a student') }}</h3>
                            <span id="active_stage_tag" class="badge-stage-tag">{{ __('High School') }}</span>
                        </div>
                        <span class="student-online-status">
                            <span class="status-green-dot"></span> {{ __('Online on platform') }}
                        </span>
                    </div>
                </div>

                <div class="chat-header-actions">
                    <button type="button" class="action-circle-btn" onclick="scrollToBottom()" title="{{ __('Jump to latest message') }}">
                        <i class="fa-solid fa-arrow-down"></i>
                    </button>
                </div>
            </div>

            <!-- كبسولات الردود الأكاديمية السريعة للمعلم (Quick Teacher Feedback) -->
            <div id="quick_teacher_prompts" class="teacher-canned-prompts-bar" style="display: none;">
                <span class="canned-label"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i> {{ __('Quick Responses:') }}</span>
                <div class="canned-scroll-lane">
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('{{ __('Well done, outstanding and 100% correct answer! 👏') }}')">
                        {{ __('Model Answer 👏') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('{{ __('Please review given parameters and apply rule step by step ✍️') }}')">
                        {{ __('Review Laws & Steps ✍️') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('{{ __('This is a very important and recurrent ministerial exam question 🎯') }}')">
                        {{ __('Important Ministerial Question 🎯') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('{{ __('I will explain this point in detail in our next class ⏰') }}')">
                        {{ __('Explain Next Class ⏰') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('{{ __('Best wishes for your high school excellence and 99%+ marks! 🌹') }}')">
                        {{ __('Encouragement 🌹') }}
                    </button>
                </div>
            </div>

            <!-- شريط الرسائل -->
            <div id="chat_messages" class="messages-flow-canvas">
                <div class="no-selection-placeholder">
                    <div class="placeholder-icon-circle">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3>{{ __('Welcome to Academic Inquiries Center') }}</h3>
                    <p>{{ __('Select a student from the sidebar to view questions and respond.') }}</p>
                </div>
            </div>

            <!-- شريط إدخال الرد -->
            <div id="input_area" class="teacher-composer-area" style="display: none;">
                <form id="chatForm" onsubmit="return false;">
                    <div class="composer-flex-container">
                        <input type="text" id="msg_input" class="teacher-msg-input" placeholder="{{ __('Type your academic response here...') }}" autocomplete="off">
                        <button type="button" class="btn-send-reply" id="btn_send" onclick="sendTeacherReply()">
                            <span>{{ __('Send Reply') }}</span>
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
    --app-height: calc(100vh - 120px);
}

.teacher-chat-wrapper {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 40px;
    box-sizing: border-box;
}

.telegram-inbox-grid {
    height: var(--app-height);
    min-height: 560px;
    display: grid;
    grid-template-columns: var(--side-width) 1fr;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

/* القائمة الجانبية */
.chat-sidebar-pane {
    background: #f8fafc;
    border-inline-end: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.sidebar-top-bar {
    padding: 16px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.sidebar-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.sidebar-headline {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.badge-total-students {
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
}

.search-input-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    inset-inline-start: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
}

#search_student {
    width: 100%;
    padding: 8px 12px 8px 36px;
    padding-inline-start: 36px;
    padding-inline-end: 12px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.84rem;
    outline: none;
    box-sizing: border-box;
}

#search_student:focus {
    border-color: #1e3a8a;
    background: #ffffff;
}

/* قائمة كروت الطلاب */
.students-scroll-list {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.student-thread-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: #ffffff;
    border: 1px solid transparent;
}

.student-thread-card:hover {
    background: #f1f5f9;
}

.student-thread-card.active {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.student-avatar-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 1rem;
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
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thread-time-tag {
    font-size: 0.7rem;
    color: #94a3b8;
}

.student-stage-subtext {
    font-size: 0.74rem;
    color: #64748b;
}

/* ساحة المحادثة */
.chat-viewport-pane {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #f8fafc;
}

.active-chat-header {
    padding: 14px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.header-student-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 1.1rem;
}

.active-student-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

.badge-stage-tag {
    background: #eff6ff;
    color: #1e3a8a;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
}

.student-online-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    color: #059669;
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
    background: #ffffff;
    color: #64748b;
    cursor: pointer;
    display: grid;
    place-items: center;
    transition: all 0.2s;
}

.action-circle-btn:hover {
    color: #1e3a8a;
    border-color: #1e3a8a;
}

/* Canned Prompts */
.teacher-canned-prompts-bar {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 8px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
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
    background: #f8fafc;
    color: #334155;
    border: 1px solid #e2e8f0;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.canned-pill:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}

/* Messages Canvas */
.messages-flow-canvas {
    flex: 1;
    padding: 20px;
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
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 1.8rem;
    margin: 0 auto 14px;
}

.bubble-item {
    max-width: 72%;
    padding: 10px 16px;
    border-radius: 14px;
    font-size: 0.9rem;
    line-height: 1.5;
    word-break: break-word;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
}

.bubble-item.teacher-sent {
    align-self: flex-start;
    background: #1e3a8a;
    color: #ffffff;
    border-bottom-inline-start-radius: 2px;
}

.bubble-item.student-incoming {
    align-self: flex-end;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    border-bottom-inline-end-radius: 2px;
}

.msg-time-tag {
    display: block;
    font-size: 0.68rem;
    margin-top: 4px;
    opacity: 0.8;
}

/* Composer Area */
.teacher-composer-area {
    padding: 12px 20px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.composer-flex-container {
    display: flex;
    gap: 10px;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    padding: 6px 12px;
}

.teacher-msg-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 0.92rem;
    color: #0f172a;
}

.btn-send-reply {
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    padding: 9px 20px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-send-reply:hover {
    background: #172554;
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
                <p class="mt-2" style="font-size: 0.85rem;">{{ __('Syncing student inquiries...') }}</p>
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
                            <h3>{{ __('No previous messages with student') }}</h3>
                            <p>{{ __('You can send guidance or study follow-up now.') }}</p>
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
        sendBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> {{ __('Sending...') }}`;

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
            alert('{{ __("Error sending reply, please try again.") }}');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<span>{{ __('Send Reply') }}</span> <i class="fa-solid fa-paper-plane"></i>`;
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

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const sid = urlParams.get('student_id');
        if (sid) {
            const card = document.getElementById('user_' + sid);
            if (card) {
                card.click();
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
</script>
@endsection
