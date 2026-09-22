@extends('layouts.app')

@section('title', __('مركز استفسارات ومراسلات الطلاب') . ' - ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="academic-inbox-wrapper">
    <div class="academic-inbox-grid" id="teacher_inbox_container">

        {{-- 1. القائمة الجانبية للطلاب (Sidebar) --}}
        <aside class="chat-sidebar-pane" id="chat_sidebar">
            <div class="sidebar-top-bar">
                <div class="sidebar-title-row">
                    <div class="sidebar-crest-icon">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div>
                        <h2 class="sidebar-headline">{{ __('استفسارات الطلاب') }}</h2>
                        <p class="sidebar-subtext">{{ __('المتابعة الدراسية والتواصل المباشر') }}</p>
                    </div>
                </div>

                {{-- البحث والفلترة --}}
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="search_student" oninput="filterStudents()" placeholder="{{ __('ابحث باسم الطالب أو الفرع...') }}" autocomplete="off">
                </div>
            </div>

            {{-- قائمة كروت الطلاب --}}
            <div class="students-scroll-list" id="student_list">
                @forelse($students as $student)
                    @php
                        $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: __('طالب');
                        $firstLetter = mb_substr($studentName, 0, 1);
                        $stageName = $student->stage->name_ar ?? (optional($student->stage)->label_ar ?? __('توجيهي فلسطين'));
                    @endphp
                    <div onclick="loadTeacherChat({{ $student->id }}, '{{ addslashes($studentName) }}', '{{ addslashes($stageName) }}')"
                         class="student-thread-card"
                         id="user_{{ $student->id }}"
                         data-student-id="{{ $student->id }}"
                         data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '') . ' ' . $stageName) }}">

                        <div class="student-avatar-box">
                            {{ $firstLetter }}
                            <span class="online-indicator-dot"></span>
                        </div>

                        <div class="student-thread-meta">
                            <div class="thread-top">
                                <strong class="student-name-text">{{ $studentName }}</strong>
                                <span class="role-sub-tag">{{ __('طالب') }}</span>
                            </div>
                            <div class="thread-bottom">
                                <span class="student-stage-subtext">{{ $stageName }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-threads-state">
                        <i class="fa-regular fa-folder-open"></i>
                        <p>{{ __('لا توجد محادثات طلاب مسجلة حالياً') }}</p>
                    </div>
                @endforelse
                <div id="no_results" class="empty-threads-state" style="display: none;">
                    <i class="fa-solid fa-user-slash"></i>
                    <p>{{ __('لم يتم العثور على أي طلاب مطابقين') }}</p>
                </div>
            </div>
        </aside>

        {{-- 2. ساحة المحادثة الرئيسية (Main Chat Pane) --}}
        <main class="chat-viewport-pane" id="chat_main">

            {{-- هيدر المحادثة النشطة --}}
            <header id="chat_header" class="active-chat-header" style="display: none;">
                <div class="header-student-profile">
                    <button type="button" class="mobile-return-btn" onclick="toggleMobileSidebar()" title="{{ __('العودة لقائمة الطلاب') }}">
                        <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
                    </button>

                    <div class="header-avatar-circle" id="active_avatar">ط</div>

                    <div class="active-student-meta">
                        <div class="name-stage-row">
                            <h3 id="active_user_name" class="active-student-title">{{ __('اختر طالباً') }}</h3>
                            <span id="active_stage_tag" class="badge-stage-tag">{{ __('توجيهي') }}</span>
                        </div>
                        <span class="student-online-status">
                            <span class="status-green-dot"></span> {{ __('طالب مسجل في المنصة') }}
                        </span>
                    </div>
                </div>

                <div class="chat-header-actions">
                    <button type="button" class="action-circle-btn" id="soundToggleBtn" onclick="toggleAudioChime()" title="{{ __('كتم/تفعيل صوت الإشعارات') }}">
                        <i class="fa-solid fa-volume-high" id="soundIcon"></i>
                    </button>
                    <button type="button" class="action-circle-btn" onclick="fetchMessages(true)" title="{{ __('تحديث المحادثة') }}">
                        <i class="fa-solid fa-rotate-right" id="refreshIcon"></i>
                    </button>
                    <button type="button" class="action-circle-btn" onclick="scrollToBottomSmooth()" title="{{ __('الانتقال لآخر رسالة') }}">
                        <i class="fa-solid fa-angles-down"></i>
                    </button>
                </div>
            </header>

            {{-- كبسولات الردود الأكاديمية السريعة للمعلم --}}
            <div id="quick_teacher_prompts" class="teacher-canned-prompts-bar" style="display: none;">
                <span class="canned-label"><i class="fa-solid fa-bolt text-amber"></i> {{ __('ردود أكاديمية سريعة:') }}</span>
                <div class="canned-scroll-lane">
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('إجابة نموذجية وممتازة 100%، استمر يا بطل! 👏')">
                        👏 إجابة نموذجية
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('أرجو مراجعة المعطيات وتطبيق القانون الوزاري خطوة بخطوة ✍️')">
                        ✍️ راجع القوانين والخطوات
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('هذا السؤال وزاري مهم ومتكرر في امتحانات الثانوية العامة 🎯')">
                        🎯 فكرة وزارية هامة
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('سأقوم بشرح هذه الجزئية بالتفصيل في الحصة القادمة ⏰')">
                        ⏰ شرح بالحصة القادمة
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('كل التوفيق والتفوق، علامتك بإذن الله 99%+ في التوجيهي 🌹')">
                        🌹 تشجيع وتفوق
                    </button>
                </div>
            </div>

            {{-- شريط تدفق الرسائل --}}
            <div id="chat_messages" class="messages-flow-canvas">
                <div class="no-selection-placeholder" id="placeholderView">
                    <div class="placeholder-icon-circle">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>{{ __('مركز استفسارات المنهاج والتواصل مع الطلاب') }}</h3>
                    <p>{{ __('اختر طالباً من القائمة الجانبية للاطلاع على أسئلته ومتابعة تحصيله الدراسي وتقديم التوجيه الأكاديمي المباشر.') }}</p>
                </div>
            </div>

            {{-- شريط إدخال الرد --}}
            <div id="input_area" class="teacher-composer-area" style="display: none;">
                <form id="chatForm" onsubmit="event.preventDefault(); sendTeacherReply();" class="composer-form-inner">
                    <div class="composer-field-wrapper">
                        <textarea id="msg_input" 
                                  class="composer-textarea"
                                  placeholder="{{ __('اكتب توجيهك أو ردك الأكاديمي للطالب هنا... (اضغط Enter للإرسال)') }}" 
                                  rows="1" 
                                  autocomplete="off"
                                  onkeydown="handleComposerKeydown(event)"
                                  oninput="autoExpandTextarea(this)"></textarea>

                        <button type="submit" id="btn_send" class="composer-send-btn" title="{{ __('إرسال الرد') }}">
                            <span>{{ __('إرسال') }}</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="composer-hint-row">
                        <span><i class="fa-regular fa-keyboard"></i> {{ __('اضغط Enter للإرسال، أو Shift + Enter لسطر جديد') }}</span>
                        <span id="charCountEl" class="font-mono text-muted">0 / 1000</span>
                    </div>
                </form>
            </div>

        </main>

    </div>
</div>

<style>
/* =========================================================
   CLASSIC ACADEMIC TEACHER INBOX (Institutional Prestige)
   ========================================================= */
.academic-inbox-wrapper {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 16px;
}

.academic-inbox-grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    height: calc(100vh - 120px);
    min-height: 540px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

/* Sidebar */
.chat-sidebar-pane {
    background: #ffffff;
    border-inline-end: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sidebar-top-bar {
    padding: 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.sidebar-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.sidebar-crest-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
}

.sidebar-headline {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.sidebar-subtext {
    font-size: 0.74rem;
    color: #64748b;
    margin: 0;
}

.search-input-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
}

.search-input-wrapper input {
    width: 100%;
    padding: 8px 34px 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    font-size: 0.84rem;
    background: #ffffff;
    transition: border-color 0.15s;
}

.search-input-wrapper input:focus {
    border-color: #1e3a8a;
}

.students-scroll-list {
    flex: 1;
    overflow-y: auto;
}

.student-thread-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background-color 0.15s;
}

.student-thread-card:hover {
    background: #f8fafc;
}

.student-thread-card.active {
    background: #eff6ff;
    border-inline-start: 4px solid #1e3a8a;
}

.student-avatar-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1rem;
    font-weight: 800;
    position: relative;
    flex-shrink: 0;
}

.online-indicator-dot {
    position: absolute;
    bottom: -2px;
    left: -2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #ffffff;
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
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.role-sub-tag {
    font-size: 0.68rem;
    color: #1e3a8a;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 700;
}

.student-stage-subtext {
    font-size: 0.74rem;
    color: #64748b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-threads-state {
    text-align: center;
    padding: 30px 16px;
    color: #94a3b8;
}

.empty-threads-state i {
    font-size: 1.8rem;
    margin-bottom: 8px;
    opacity: 0.5;
}

/* Chat Main */
.chat-viewport-pane {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #f8fafc;
    position: relative;
}

.active-chat-header {
    padding: 12px 20px;
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

.mobile-return-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.1rem;
    color: #1e3a8a;
    cursor: pointer;
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

.name-stage-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
}

.active-student-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.badge-stage-tag {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1e3a8a;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 1px 7px;
    border-radius: 4px;
}

.student-online-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.76rem;
    color: #059669;
    font-weight: 600;
}

.status-green-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
}

.chat-header-actions {
    display: flex;
    gap: 8px;
}

.action-circle-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #475569;
    display: grid;
    place-items: center;
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.15s;
}

.action-circle-btn:hover {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #93c5fd;
}

/* Canned Prompts */
.teacher-canned-prompts-bar {
    padding: 8px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    flex-shrink: 0;
}

.canned-label {
    font-size: 0.78rem;
    font-weight: 800;
    color: #334155;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.canned-scroll-lane {
    display: flex;
    gap: 8px;
    white-space: nowrap;
}

.canned-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}

.canned-pill:hover {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #93c5fd;
}

/* Messages Canvas */
.messages-flow-canvas {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bubble-item {
    max-width: 75%;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.92rem;
    line-height: 1.6;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.teacher-sent {
    align-self: flex-end;
    background: #1e3a8a;
    color: #ffffff;
    border: 1px solid #1e3a8a;
    border-top-left-radius: 2px;
}

.student-incoming {
    align-self: flex-start;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    border-top-right-radius: 2px;
}

.msg-time-tag {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 4px;
    font-size: 0.7rem;
}

.teacher-sent .msg-time-tag {
    color: #bfdbfe;
}

.student-incoming .msg-time-tag {
    color: #64748b;
}

.no-selection-placeholder {
    margin: auto;
    text-align: center;
    max-width: 440px;
    padding: 30px 16px;
    color: #64748b;
}

.placeholder-icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 1.8rem;
    margin: 0 auto 12px;
}

.no-selection-placeholder h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}

/* Composer */
.teacher-composer-area {
    padding: 12px 18px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.composer-field-wrapper {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.composer-textarea {
    flex: 1;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    outline: none;
    font-size: 0.92rem;
    color: #0f172a;
    line-height: 1.5;
    resize: none;
    max-height: 120px;
    transition: border-color 0.15s;
}

.composer-textarea:focus {
    border-color: #1e3a8a;
    background: #ffffff;
}

.composer-send-btn {
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 11px 22px;
    font-weight: 800;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.15s;
    flex-shrink: 0;
}

.composer-send-btn:hover {
    background: #0f172a;
}

.composer-hint-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    font-size: 0.72rem;
    color: #64748b;
}

/* Responsive */
@media (max-width: 768px) {
    .academic-inbox-grid {
        grid-template-columns: 1fr;
        height: calc(100vh - 80px);
        border-radius: 0;
        border: none;
    }
    .chat-sidebar-pane.mobile-hidden { display: none; }
    .chat-viewport-pane.mobile-hidden { display: none; }
    .mobile-return-btn { display: block; }
    .bubble-item { max-width: 88%; }
}
</style>

<script>
    let activeStudentId = null;
    let pollInterval = null;
    let lastContentHash = "";
    let soundEnabled = true;

    const audioChime = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbqWE2M1yct9e6ei8xWpu50rJ3LS5ZoLTIsHMwLlimtMWmcTItV5+wvKZwLCxVoK6yqG0vLFKcq62nbjAsUKKkpqJuLS1No6Ghnm8tLUueoJ+cbistTZubm5prKy1MmpeVlmcrLEuXlZSUZiwtSpOUk5JkLC1KkpKSkmMsLEqSkZCRZCws');

    function toggleAudioChime() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        if (icon) {
            icon.className = soundEnabled ? 'fa-solid fa-volume-high' : 'fa-solid fa-volume-xmark';
        }
    }

    function playAudioChime() {
        if (!soundEnabled) return;
        try {
            audioChime.currentTime = 0;
            audioChime.play().catch(() => {});
        } catch(e) {}
    }

    function loadTeacherChat(id, name, stage) {
        activeStudentId = id;
        lastContentHash = "";

        document.getElementById('chat_header').style.display = 'flex';
        document.getElementById('quick_teacher_prompts').style.display = 'flex';
        document.getElementById('input_area').style.display = 'block';

        document.getElementById('active_user_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);
        if (stage) {
            document.getElementById('active_stage_tag').innerText = stage;
        }

        document.querySelectorAll('.student-thread-card').forEach(c => c.classList.remove('active'));
        const card = document.getElementById('user_' + id);
        if (card) card.classList.add('active');

        // On mobile, show chat pane and hide sidebar
        if (window.innerWidth <= 768) {
            document.getElementById('chat_sidebar').classList.add('mobile-hidden');
            document.getElementById('chat_main').classList.remove('mobile-hidden');
        }

        fetchMessages(true);

        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3500);
    }

    function toggleMobileSidebar() {
        if (window.innerWidth <= 768) {
            document.getElementById('chat_sidebar').classList.remove('mobile-hidden');
            document.getElementById('chat_main').classList.add('mobile-hidden');
        }
    }

    function fetchMessages(forceScroll = false) {
        if (!activeStudentId) return;

        const refreshIcon = document.getElementById('refreshIcon');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        axios.get('/teacher/messages/' + activeStudentId)
            .then(res => {
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                const currentHash = JSON.stringify(messages);
                if (currentHash === lastContentHash && !forceScroll) return;

                const isNearBottom = box.scrollHeight - box.scrollTop <= box.clientHeight + 120;
                const isNewFromStudent = lastContentHash !== "" && messages.length > 0 && messages[messages.length - 1].sender_type === 'student';

                lastContentHash = currentHash;

                if (messages.length === 0) {
                    box.innerHTML = `
                        <div class="no-selection-placeholder">
                            <div class="placeholder-icon-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                            <h3>{{ __('لا توجد رسائل سابقة مع الطالب') }}</h3>
                            <p>{{ __('يمكنك بدء المحادثة وتوجيه الطالب دراسياً الآن.') }}</p>
                        </div>`;
                    return;
                }

                box.innerHTML = '';
                messages.forEach(m => {
                    const sender = (m.sender_type || '').toLowerCase().trim();
                    const isMe = (sender === 'teacher');
                    const time = m.created_at_formatted || 'الآن';

                    const item = document.createElement('div');
                    item.className = `bubble-item ${isMe ? 'teacher-sent' : 'student-incoming'}`;
                    item.innerHTML = `
                        <div style="white-space: pre-wrap;">${escapeHtml(m.message)}</div>
                        <div class="msg-time-tag font-mono">
                            <span>${isMe ? '{{ __("أنت (المعلم)") }}' : '{{ __("الطالب") }}'}</span>
                            <span>•</span>
                            <span>${time} ${isMe ? '✓✓' : ''}</span>
                        </div>
                    `;
                    box.appendChild(item);
                });

                if (isNewFromStudent) {
                    playAudioChime();
                }

                if (forceScroll || isNearBottom) {
                    scrollToBottomSmooth();
                }
            })
            .catch(err => console.error("Fetch Messages Error: ", err))
            .finally(() => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            });
    }

    function sendTeacherReply() {
        const input = document.getElementById('msg_input');
        const text = input.value.trim();
        const sendBtn = document.getElementById('btn_send');
        const charCountEl = document.getElementById('charCountEl');

        if (!text || !activeStudentId) return;

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        input.value = '';
        input.style.height = 'auto';
        if (charCountEl) charCountEl.innerText = '0 / 1000';

        // Optimistic render
        const box = document.getElementById('chat_messages');
        const ph = box.querySelector('.no-selection-placeholder');
        if (ph) box.innerHTML = '';

        const optimisticBubble = document.createElement('div');
        optimisticBubble.className = 'bubble-item teacher-sent';
        optimisticBubble.innerHTML = `
            <div style="white-space: pre-wrap;">${escapeHtml(text)}</div>
            <div class="msg-time-tag font-mono">
                <span>{{ __("أنت (المعلم)") }}</span>
                <span>•</span>
                <span>الآن ✓</span>
            </div>
        `;
        box.appendChild(optimisticBubble);
        scrollToBottomSmooth();

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
            fetchMessages(true);
        })
        .catch(err => {
            alert('{{ __("حدث خطأ أثناء إرسال الرد، يرجى المحاولة مرة أخرى.") }}');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<span>{{ __('إرسال') }}</span> <i class="fa-solid fa-paper-plane"></i>`;
            input.focus();
        });
    }

    function insertTeacherCanned(text) {
        const input = document.getElementById('msg_input');
        input.value = text;
        autoExpandTextarea(input);
        input.focus();
    }

    function autoExpandTextarea(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        const counter = document.getElementById('charCountEl');
        if (counter) {
            counter.innerText = `${el.value.length} / 1000`;
        }
    }

    function handleComposerKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendTeacherReply();
        }
    }

    function scrollToBottomSmooth() {
        const box = document.getElementById('chat_messages');
        if (box) box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });
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
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
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
