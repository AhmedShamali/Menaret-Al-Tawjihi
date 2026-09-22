@extends('layouts.app')

@section('title', __('المحادثات والتواصل الأكاديمي مع المعلمين') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="academic-teacher-chat-container">
    <div class="chat-main-card">

        {{-- القائمة الجانبية للمعلمين --}}
        <aside class="teachers-sidebar-pane" id="teachers_sidebar">
            <div class="sidebar-top-bar">
                <div class="sidebar-brand-title">
                    <div class="brand-avatar-icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <h3 class="brand-heading">{{ __('الكادر التعليمي') }}</h3>
                        <p class="brand-sub">{{ __('المراسلات الإدارية المباشرة') }}</p>
                    </div>
                </div>

                {{-- بحث عن معلم --}}
                <div class="sidebar-search-box">
                    <i class="fa-solid fa-magnifying-glass search-ico"></i>
                    <input type="text" id="searchTeacherInput" oninput="filterTeachersList()" placeholder="{{ __('بحث باسم المعلم أو المادة...') }}" autocomplete="off">
                </div>
            </div>

            <div class="teachers-scroll-list" id="teachers_list">
                @forelse($teachers as $teacher)
                    @php
                        $tName = $teacher->name_ar ?? $teacher->name;
                        $tFirst = mb_substr($tName, 0, 1);
                        $isActive = isset($selectedTeacher) && $selectedTeacher->id == $teacher->id;
                    @endphp
                    <a href="{{ route('admin.teachers.chat', ['teacher_id' => $teacher->id]) }}"
                       class="teacher-list-card {{ $isActive ? 'active' : '' }}"
                       id="teacher_card_{{ $teacher->id }}"
                       data-search="{{ mb_strtolower($tName . ' ' . ($teacher->email ?? '') . ' ' . ($teacher->subject_name ?? '')) }}">
                        <div class="teacher-avatar">
                            {{ $tFirst }}
                            <span class="online-indicator-dot"></span>
                        </div>
                        <div class="teacher-details">
                            <div class="name-line">
                                <h4 class="t-name">{{ $tName }}</h4>
                                <span class="role-badge">{{ __('معلم') }}</span>
                            </div>
                            <span class="t-sub font-mono" dir="ltr">{{ $teacher->email ?? 'Teacher' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="empty-sidebar-box">
                        <i class="fa-solid fa-user-slash"></i>
                        <p>{{ __('لا يوجد معلمون مسجلون حالياً') }}</p>
                    </div>
                @endforelse
                <div id="noTeacherResults" class="empty-sidebar-box" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>{{ __('لم يتم العثور على أي نتائج مطابقة') }}</p>
                </div>
            </div>
        </aside>

        {{-- ساحة المحادثة الرئيسية --}}
        <main class="chat-viewport-pane" id="chat_main">
            @if(isset($selectedTeacher))
                {{-- هيدر المحادثة النشطة --}}
                <header class="active-chat-header">
                    <div class="active-profile-group">
                        <button type="button" class="mobile-return-btn" onclick="toggleMobileTeacherSidebar()" title="{{ __('العودة لقائمة المعلمين') }}">
                            <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
                        </button>

                        <div class="active-avatar">
                            {{ mb_substr($selectedTeacher->name_ar ?? $selectedTeacher->name, 0, 1) }}
                            <span class="online-indicator-dot"></span>
                        </div>

                        <div class="active-info">
                            <div class="name-and-tag">
                                <h3 class="active-name">{{ $selectedTeacher->name_ar ?? $selectedTeacher->name }}</h3>
                                <span class="inst-tag"><i class="fa-solid fa-graduation-cap"></i> {{ __('كادر التوجيهي المعتمد') }}</span>
                            </div>
                            <div class="status-sub-line">
                                <span class="pulse-emerald"></span>
                                <span>{{ __('متاح للتنسيق الإداري والأكاديمي المباشر') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="header-tools-group">
                        <button type="button" class="tool-btn" id="soundToggleBtn" onclick="toggleAudioChime()" title="{{ __('كتم/تفعيل صوت الرسائل') }}">
                            <i class="fa-solid fa-volume-high" id="soundIcon"></i>
                        </button>
                        <button type="button" class="tool-btn" onclick="fetchTeacherMessages(true)" title="{{ __('تحديث الرسائل') }}">
                            <i class="fa-solid fa-rotate-right" id="refreshIcon"></i>
                        </button>
                        <button type="button" class="tool-btn" onclick="scrollToBottomSmooth()" title="{{ __('الانتقال لآخر رسالة') }}">
                            <i class="fa-solid fa-angles-down"></i>
                        </button>
                    </div>
                </header>

                {{-- شريط الردود الإدارية السريعة --}}
                <div class="admin-canned-bar">
                    <span class="canned-title"><i class="fa-solid fa-bolt text-amber"></i> {{ __('ردود سريعة:') }}</span>
                    <div class="canned-scroll-lane">
                        <button type="button" class="canned-pill" onclick="insertAdminPrompt('السلام عليكم أستاذنا الفاضل، تم اعتماد الاختبار بنجاح ونشره للطلاب ✅')">
                            ✅ تم اعتماد الاختبار
                        </button>
                        <button type="button" class="canned-pill" onclick="insertAdminPrompt('السلام عليكم، تم مراجعة الشروحات والملازم المرفقة وتنسيقها على المنصة 📄')">
                            📄 تم اعتماد الملازم
                        </button>
                        <button type="button" class="canned-pill" onclick="insertAdminPrompt('السلام عليكم، تم تحويل المستحقات المالية ومطابقة كشف الطلاب للشهر الحالي 💰')">
                            💰 تم صرف المستحقات
                        </button>
                        <button type="button" class="canned-pill" onclick="insertAdminPrompt('بارك الله في جهودكم وعطائكم المتميز مع طلبة التوجيهي 🌹')">
                            🌹 شكر وتقدير
                        </button>
                    </div>
                </div>

                {{-- ساحة تدفق الرسائل --}}
                <div id="chat_messages_box" class="messages-flow-canvas">
                    @forelse($messages as $msg)
                        @php
                            $isMe = ($msg->sender_type === 'admin');
                            $timeStr = $msg->created_at ? $msg->created_at->timezone('Asia/Gaza')->format('h:i A') : 'الآن';
                        @endphp
                        <div class="bubble-row {{ $isMe ? 'bubble-admin-sent' : 'bubble-teacher-received' }}">
                            <div class="classic-chat-bubble">
                                <div class="bubble-text">{{ $msg->message }}</div>
                                <div class="bubble-meta font-mono">
                                    <span>{{ $isMe ? __('إدارة المنصة') : ($selectedTeacher->name_ar ?? $selectedTeacher->name) }}</span>
                                    <span>•</span>
                                    <span>{{ $timeStr }} {{ $isMe ? '✓✓' : '' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-conversation-state" id="noMessagesPlaceholder">
                            <div class="empty-circle-icon">
                                <i class="fa-regular fa-comments"></i>
                            </div>
                            <h4>{{ __('لا توجد رسائل سابقة مع المعلم') }}</h4>
                            <p>{{ __('يمكنك بدء المحادثة وإرسال التوجيهات أو الملاحظات الأكاديمية الآن.') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- شريط الإدخال المتطور --}}
                <div class="composer-container-pane">
                    <form id="sendMessageForm" onsubmit="event.preventDefault(); sendAdminMessage();" class="composer-form-box">
                        <div class="composer-input-row">
                            <textarea id="messageInput"
                                      class="composer-textarea"
                                      placeholder="{{ __('اكتب رسالتك أو توجيهاتك للمعلم هنا... (اضغط Enter للإرسال)') }}"
                                      rows="1"
                                      autocomplete="off"
                                      onkeydown="handleComposerKeydown(event)"
                                      oninput="autoExpandTextarea(this)"></textarea>

                            <button type="submit" id="btnSendMsg" class="composer-send-btn" title="{{ __('إرسال الرسالة') }}">
                                <span>{{ __('إرسال') }}</span>
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="composer-footer-bar">
                            <span><i class="fa-regular fa-keyboard"></i> {{ __('اضغط Enter للإرسال المباشر، أو Shift + Enter لسطر جديد') }}</span>
                            <span id="charCountDisplay" class="font-mono text-muted">0 / 1000</span>
                        </div>
                    </form>
                </div>
            @else
                {{-- شاشة الترحيب عند عدم اختيار معلم --}}
                <div class="welcome-selection-pane">
                    <div class="welcome-crest-box">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3>{{ __('مركز المراسلات والتنسيق مع الكادر التعليمي') }}</h3>
                    <p>{{ __('اختر معلماً من القائمة الجانبية لبدء المحادثة ومتابعة سير المنهاج والامتحانات واعتماد المحتويات.') }}</p>
                    <div class="quick-stats-pills">
                        <span class="stat-pill"><i class="fa-solid fa-users"></i> {{ count($teachers) }} {{ __('معلم مسجل') }}</span>
                        <span class="stat-pill"><i class="fa-solid fa-shield-check"></i> {{ __('اعتماد فوري للتواصل') }}</span>
                    </div>
                </div>
            @endif
        </main>

    </div>
</div>

<style>
/* =========================================================
   CLASSIC ACADEMIC TEACHER CHAT STYLING
   ========================================================= */
.academic-teacher-chat-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 16px;
}

.chat-main-card {
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
.teachers-sidebar-pane {
    background: #ffffff;
    border-inline-end: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sidebar-top-bar {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.sidebar-brand-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.brand-avatar-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
}

.brand-heading {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.brand-sub {
    font-size: 0.74rem;
    color: #64748b;
    margin: 0;
}

.sidebar-search-box {
    position: relative;
}

.search-ico {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
}

.sidebar-search-box input {
    width: 100%;
    padding: 8px 34px 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    font-size: 0.84rem;
    background: #ffffff;
    transition: border-color 0.15s;
}

.sidebar-search-box input:focus {
    border-color: #1e3a8a;
}

.teachers-scroll-list {
    flex: 1;
    overflow-y: auto;
}

.teacher-list-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: #334155;
    transition: background-color 0.15s;
}

.teacher-list-card:hover {
    background: #f8fafc;
}

.teacher-list-card.active {
    background: #eff6ff;
    border-inline-start: 4px solid #1e3a8a;
}

.teacher-avatar {
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

.teacher-details {
    flex: 1;
    min-width: 0;
}

.name-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2px;
}

.t-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.role-badge {
    font-size: 0.68rem;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 700;
}

.t-sub {
    font-size: 0.74rem;
    color: #64748b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-sidebar-box {
    text-align: center;
    padding: 30px 16px;
    color: #94a3b8;
}

.empty-sidebar-box i {
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

.active-profile-group {
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

.active-avatar {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 1.1rem;
    position: relative;
}

.name-and-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
}

.active-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.inst-tag {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1e3a8a;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 1px 7px;
    border-radius: 4px;
}

.status-sub-line {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.76rem;
    color: #059669;
    font-weight: 600;
}

.pulse-emerald {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
}

.header-tools-group {
    display: flex;
    gap: 8px;
}

.tool-btn {
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

.tool-btn:hover {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #93c5fd;
}

/* Canned Prompts */
.admin-canned-bar {
    padding: 8px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    flex-shrink: 0;
}

.canned-title {
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

/* Messages Flow */
.messages-flow-canvas {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bubble-row {
    display: flex;
    width: 100%;
}

.bubble-admin-sent {
    justify-content: flex-end;
}

.bubble-teacher-received {
    justify-content: flex-start;
}

.classic-chat-bubble {
    max-width: 75%;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.92rem;
    line-height: 1.6;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.bubble-admin-sent .classic-chat-bubble {
    background: #1e3a8a;
    color: #ffffff;
    border: 1px solid #1e3a8a;
    border-top-left-radius: 2px;
}

.bubble-teacher-received .classic-chat-bubble {
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    border-top-right-radius: 2px;
}

.bubble-text {
    white-space: pre-wrap;
}

.bubble-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 4px;
    font-size: 0.7rem;
}

.bubble-admin-sent .bubble-meta {
    color: #bfdbfe;
}

.bubble-teacher-received .bubble-meta {
    color: #64748b;
}

.empty-conversation-state {
    margin: auto;
    text-align: center;
    max-width: 440px;
    padding: 30px 16px;
    color: #64748b;
}

.empty-circle-icon {
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

.empty-conversation-state h4 {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}

/* Composer */
.composer-container-pane {
    padding: 12px 18px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.composer-input-row {
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

.composer-footer-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    font-size: 0.72rem;
    color: #64748b;
}

/* Welcome selection pane */
.welcome-selection-pane {
    margin: auto;
    text-align: center;
    max-width: 460px;
    padding: 40px 20px;
    color: #64748b;
}

.welcome-crest-box {
    width: 68px;
    height: 68px;
    border-radius: 16px;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 2rem;
    margin: 0 auto 16px;
}

.welcome-selection-pane h3 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 8px;
}

.quick-stats-pills {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 18px;
}

.stat-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Responsive */
@media (max-width: 768px) {
    .chat-main-card {
        grid-template-columns: 1fr;
        height: calc(100vh - 80px);
        border-radius: 0;
        border: none;
    }
    @isset($selectedTeacher)
        .teachers-sidebar-pane { display: none; }
        .chat-viewport-pane { display: flex; }
        .mobile-return-btn { display: block; }
    @else
        .teachers-sidebar-pane { display: flex; }
        .chat-viewport-pane { display: none; }
    @endisset
    .classic-chat-bubble { max-width: 88%; }
}
</style>

@if(isset($selectedTeacher))
<script>
    const chatFeed = document.getElementById('chat_messages_box');
    const msgInput = document.getElementById('messageInput');
    const charCountDisplay = document.getElementById('charCountDisplay');
    const teacherId = {{ $selectedTeacher->id }};
    let soundEnabled = true;
    let lastContentHash = "";

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

    function insertAdminPrompt(text) {
        msgInput.value = text;
        autoExpandTextarea(msgInput);
        msgInput.focus();
    }

    function autoExpandTextarea(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        if (charCountDisplay) {
            charCountDisplay.innerText = `${el.value.length} / 1000`;
        }
    }

    function handleComposerKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendAdminMessage();
        }
    }

    function scrollToBottomSmooth() {
        if (chatFeed) {
            chatFeed.scrollTo({ top: chatFeed.scrollHeight, behavior: 'smooth' });
        }
    }

    function toggleMobileTeacherSidebar() {
        window.location.href = "{{ route('admin.teachers.chat') }}";
    }

    function fetchTeacherMessages(forceScroll = false) {
        const refreshIcon = document.getElementById('refreshIcon');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        axios.get("/admin/teachers/" + teacherId + "/messages")
            .then(res => {
                const messages = res.data.messages || [];
                const placeholder = document.getElementById('noMessagesPlaceholder');

                if (messages.length === 0) {
                    if (placeholder) placeholder.style.display = 'block';
                    return;
                }

                if (placeholder) placeholder.style.display = 'none';

                const newHash = JSON.stringify(messages);
                if (newHash === lastContentHash && !forceScroll) return;

                const isNearBottom = chatFeed.scrollHeight - chatFeed.scrollTop <= chatFeed.clientHeight + 120;
                const isNewFromTeacher = lastContentHash !== "" && messages.length > 0 && messages[messages.length - 1].sender_type !== 'admin';

                lastContentHash = newHash;

                let html = '';
                messages.forEach(m => {
                    const isMe = (m.sender_type === 'admin');
                    const time = m.created_at_formatted || 'الآن';
                    html += `
                        <div class="bubble-row ${isMe ? 'bubble-admin-sent' : 'bubble-teacher-received'}">
                            <div class="classic-chat-bubble">
                                <div class="bubble-text">${escapeHtml(m.message)}</div>
                                <div class="bubble-meta font-mono">
                                    <span>${isMe ? '{{ __("إدارة المنصة") }}' : '{{ addslashes($selectedTeacher->name_ar ?? $selectedTeacher->name) }}'}</span>
                                    <span>•</span>
                                    <span>${time} ${isMe ? '✓✓' : ''}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                chatFeed.innerHTML = html;

                if (isNewFromTeacher) {
                    playAudioChime();
                }

                if (forceScroll || isNearBottom) {
                    chatFeed.scrollTop = chatFeed.scrollHeight;
                }
            })
            .catch(err => {
                console.error("Fetch teacher messages error: ", err);
            })
            .finally(() => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            });
    }

    function sendAdminMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        const sendBtn = document.getElementById('btnSendMsg');
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        msgInput.value = '';
        msgInput.style.height = 'auto';
        if (charCountDisplay) charCountDisplay.innerText = '0 / 1000';

        const placeholder = document.getElementById('noMessagesPlaceholder');
        if (placeholder) placeholder.style.display = 'none';

        const optimisticBubble = document.createElement('div');
        optimisticBubble.className = 'bubble-row bubble-admin-sent';
        optimisticBubble.innerHTML = `
            <div class="classic-chat-bubble">
                <div class="bubble-text">${escapeHtml(text)}</div>
                <div class="bubble-meta font-mono">
                    <span>{{ __("إدارة المنصة") }}</span>
                    <span>•</span>
                    <span>الآن ✓</span>
                </div>
            </div>
        `;
        chatFeed.appendChild(optimisticBubble);
        chatFeed.scrollTop = chatFeed.scrollHeight;

        axios.post("{{ route('admin.teachers.send') }}", {
            teacher_id: teacherId,
            message: text,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            fetchTeacherMessages(true);
        })
        .catch(err => {
            alert("{{ __('تعذر إرسال الرسالة، يرجى المحاولة مرة أخرى.') }}");
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = `<span>{{ __('إرسال') }}</span> <i class="fa-solid fa-paper-plane"></i>`;
            msgInput.focus();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener('DOMContentLoaded', () => {
        scrollToBottomSmooth();
        fetchTeacherMessages(true);
        setInterval(() => fetchTeacherMessages(false), 3500);
    });
</script>
@endif

<script>
function filterTeachersList() {
    const query = document.getElementById('searchTeacherInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.teacher-list-card');
    let count = 0;

    cards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        if (searchData.includes(query)) {
            card.style.display = 'flex';
            count++;
        } else {
            card.style.display = 'none';
        }
    });

    const noResults = document.getElementById('noTeacherResults');
    if (noResults) {
        noResults.style.display = (count === 0 && cards.length > 0) ? 'block' : 'none';
    }
}
</script>
@endsection
