@extends('layouts.app')

@section('title', __('مركز استفسارات ومراسلات الطلاب') . ' - ' . config('app.name', 'Step by Step'))
@section('is_chat', true)

@section('content')
<div class="academic-inbox-wrapper">
    <div class="academic-inbox-grid" id="teacher_inbox_container">

        {{-- 1. القائمة الجانبية للطلاب (Sidebar) --}}
        <aside class="chat-sidebar-pane" id="chat_sidebar">
            <div class="sidebar-top-bar">
                <div class="sidebar-title-row">
                    <div class="brand-title-group">
                        <div class="sidebar-crest-icon">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <h2 class="sidebar-headline">{{ __('استفسارات الطلاب') }}</h2>
                            <p class="sidebar-subtext">{{ __('المتابعة الدراسية والتواصل المباشر') }}</p>
                        </div>
                    </div>
                    @php
                        $totalUnread = $students->sum('unread_count');
                    @endphp
                    @if($totalUnread > 0)
                        <span class="total-unread-badge" id="global_unread_badge">
                            {{ $totalUnread }} {{ __('جديدة') }}
                        </span>
                    @endif
                </div>

                {{-- البحث والفلترة --}}
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="search_student" oninput="filterStudents()" placeholder="{{ __('ابحث باسم الطالب أو الفرع...') }}" autocomplete="off">
                    <button type="button" id="clear_search_btn" class="clear-search-btn" onclick="clearSearch()" style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- تبويبات التصفية (الكل / غير مقروءة) --}}
                <div class="sidebar-filter-tabs">
                    <button type="button" class="filter-tab-btn active" data-filter="all" onclick="setStudentFilter('all')">
                        {{ __('الكل') }} <span class="tab-count">({{ count($students) }})</span>
                    </button>
                    <button type="button" class="filter-tab-btn" data-filter="unread" onclick="setStudentFilter('unread')">
                        {{ __('غير مقروءة') }} <span class="tab-count" id="unread_tab_count">({{ $totalUnread }})</span>
                    </button>
                </div>
            </div>

            {{-- قائمة كروت الطلاب --}}
            <div class="students-scroll-list" id="student_list">
                @forelse($students as $student)
                    @php
                        $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: __('طالب');
                        $firstLetter = mb_substr($studentName, 0, 1);
                        $stageName = ($student instanceof \App\Models\Student && $student->stage)
                            ? ($student->stage->label_ar ?? $student->stage->name_ar ?? __('توجيهي فلسطين'))
                            : ((isset($student->stage) && is_object($student->stage))
                                ? ($student->stage->label_ar ?? $student->stage->name_ar ?? __('توجيهي فلسطين'))
                                : (__('توجيهي فلسطين')));
                        $hasUnread = ($student->unread_count ?? 0) > 0;
                        $lastText = $student->last_message ?? '';
                        $lastTime = $student->last_message_time ?? '';
                        $isMe = ($student->last_sender_type === 'teacher');
                    @endphp
                    <div onclick="loadTeacherChat({{ $student->id }}, '{{ addslashes($studentName) }}', '{{ addslashes($stageName) }}')"
                         class="student-thread-card {{ $hasUnread ? 'has-unread' : '' }} {{ (isset($selectedStudentId) && $selectedStudentId == $student->id) ? 'active' : '' }}"
                         id="user_{{ $student->id }}"
                         data-student-id="{{ $student->id }}"
                         data-unread="{{ $hasUnread ? '1' : '0' }}"
                         data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '') . ' ' . $stageName . ' ' . $lastText) }}">

                        <div class="student-avatar-box">
                            {{ $firstLetter }}
                            <span class="online-indicator-dot"></span>
                        </div>

                        <div class="student-thread-meta">
                            <div class="thread-top">
                                <strong class="student-name-text" title="{{ $studentName }}">{{ $studentName }}</strong>
                                <span class="thread-time-tag" id="time_{{ $student->id }}">{{ $lastTime }}</span>
                            </div>
                            <div class="thread-mid">
                                <span class="student-stage-subtext">{{ $stageName }}</span>
                            </div>
                            <div class="thread-bottom">
                                <span class="last-snippet-text" id="snippet_{{ $student->id }}">
                                    @if($student->last_message)
                                        @if($isMe)
                                            <strong class="me-prefix">{{ __('أنت') }}: </strong>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit($lastText, 32) }}
                                    @else
                                        <span class="no-msg-hint">{{ __('لا توجد رسائل سابقة') }}</span>
                                    @endif
                                </span>
                                <span class="unread-counter-pill" id="badge_{{ $student->id }}" style="{{ $hasUnread ? '' : 'display: none;' }}">
                                    {{ $student->unread_count ?? 0 }}
                                </span>
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
        <main class="chat-viewport-pane mobile-hidden" id="chat_main">

            {{-- هيدر المحادثة النشطة --}}
            <header id="chat_header" class="active-chat-header" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="header-student-profile">
                    <button type="button" class="mobile-return-btn" onclick="toggleMobileSidebar()" title="{{ __('العودة لقائمة الطلاب') }}">
                        <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
                    </button>

                    <div class="header-avatar-circle" id="active_avatar">
                        {{ isset($selectedStudent) ? mb_substr($selectedStudent->name_ar ?? $selectedStudent->name ?? 'ط', 0, 1) : 'ط' }}
                    </div>

                    <div class="active-student-meta">
                        <div class="name-stage-row">
                            <h3 id="active_user_name" class="active-student-title">
                                {{ isset($selectedStudent) ? ($selectedStudent->name_ar ?? $selectedStudent->name ?? 'طالب') : __('اختر طالباً') }}
                            </h3>
                            <span id="active_stage_tag" class="badge-stage-tag">
                                {{ isset($selectedStudent) && $selectedStudent->stage ? ($selectedStudent->stage->label_ar ?? $selectedStudent->stage->name_ar ?? 'توجيهي') : 'توجيهي' }}
                            </span>
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
            <div id="quick_teacher_prompts" class="teacher-canned-prompts-bar" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <span class="canned-label"><i class="fa-solid fa-bolt text-amber"></i> {{ __('ردود أكاديمية سريعة:') }}</span>
                <div class="canned-scroll-lane">
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('إجابة نموذجية وممتازة 100%، استمر يا بطل! 👏')">
                        👏 {{ __('إجابة نموذجية') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('أرجو مراجعة المعطيات وتطبيق القانون الوزاري خطوة بخطوة ✍️')">
                        ✍️ {{ __('راجع القوانين والخطوات') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('هذا السؤال وزاري مهم ومتكرر في امتحانات الثانوية العامة 🎯')">
                        🎯 {{ __('فكرة وزارية هامة') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('سأقوم بشرح هذه الجزئية بالتفصيل في الحصة القادمة ⏰')">
                        ⏰ {{ __('شرح بالحصة القادمة') }}
                    </button>
                    <button type="button" class="canned-pill" onclick="insertTeacherCanned('كل التوفيق والتفوق، علامتك بإذن الله 99%+ في التوجيهي 🌹')">
                        🌹 {{ __('تشجيع وتفوق') }}
                    </button>
                </div>
            </div>

            {{-- شريط تدفق الرسائل --}}
            <div id="chat_messages" class="messages-flow-canvas">
                <div class="no-selection-placeholder" id="placeholderView" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: none;' : 'display: block;' }}">
                    <div class="placeholder-icon-circle">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>{{ __('مركز استفسارات المنهاج والتواصل مع الطلاب') }}</h3>
                    <p>{{ __('اختر طالباً من القائمة الجانبية للاطلاع على أسئلته ومتابعة تحصيله الدراسي وتقديم التوجيه الأكاديمي المباشر.') }}</p>
                </div>
            </div>

            {{-- شريط إدخال الرد --}}
            <div id="input_area" class="teacher-composer-area" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: block;' : 'display: none;' }}">
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
   CLASSIC ACADEMIC TEACHER INBOX (Modern & Fully Responsive)
   ========================================================= */
.academic-inbox-wrapper {
    width: 100%;
    max-width: 100%;
    height: 100%;
    min-height: 0;
    margin: 0 auto;
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-sizing: border-box;
}

.academic-inbox-grid {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: 100%;
    max-height: 100%;
    min-height: 0;
    flex: 1;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

/* ==================== 1. SIDEBAR ==================== */
.chat-sidebar-pane {
    background: #ffffff;
    border-inline-end: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
}

.sidebar-top-bar {
    padding: 14px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.sidebar-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
}

.brand-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-crest-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
}

.sidebar-headline {
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.2;
}

.sidebar-subtext {
    font-size: 0.72rem;
    color: #64748b;
    margin: 0;
}

.total-unread-badge {
    background: #ef4444;
    color: #ffffff;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 999px;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    animation: pulseBadge 2s infinite;
}

@keyframes pulseBadge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Search Box */
.search-input-wrapper {
    position: relative;
    margin-bottom: 10px;
}

.search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.82rem;
    pointer-events: none;
}

.search-input-wrapper input {
    width: 100%;
    padding: 8px 34px 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    outline: none;
    font-size: 0.84rem;
    background: #ffffff;
    transition: all 0.15s ease;
    box-sizing: border-box;
}

.search-input-wrapper input:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.clear-search-btn {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 0.8rem;
    padding: 4px;
}

.clear-search-btn:hover {
    color: #0f172a;
}

/* Filter Tabs */
.sidebar-filter-tabs {
    display: flex;
    gap: 6px;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 8px;
}

.filter-tab-btn {
    flex: 1;
    padding: 5px 8px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: all 0.15s ease;
}

.filter-tab-btn.active {
    background: #ffffff;
    color: #1e3a8a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

.tab-count {
    font-size: 0.7rem;
    opacity: 0.8;
}

/* Student List */
.students-scroll-list {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.student-thread-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: all 0.15s ease;
    position: relative;
}

.student-thread-card:hover {
    background: #f8fafc;
}

.student-thread-card.active {
    background: #eff6ff;
    border-inline-start: 4px solid #1e3a8a;
}

.student-avatar-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.05rem;
    font-weight: 800;
    position: relative;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(30, 58, 138, 0.15);
}

.online-indicator-dot {
    position: absolute;
    bottom: -2px;
    left: -2px;
    width: 11px;
    height: 11px;
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
    gap: 6px;
    margin-bottom: 2px;
}

.student-name-text {
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thread-time-tag {
    font-size: 0.68rem;
    color: #94a3b8;
    white-space: nowrap;
    flex-shrink: 0;
}

.thread-mid {
    margin-bottom: 4px;
}

.student-stage-subtext {
    font-size: 0.72rem;
    color: #1e3a8a;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 700;
    display: inline-block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thread-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.last-snippet-text {
    font-size: 0.76rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
    min-width: 0;
}

.me-prefix {
    color: #1e3a8a;
    font-weight: 800;
}

.no-msg-hint {
    color: #94a3b8;
    font-style: italic;
}

.unread-counter-pill {
    background: #ef4444;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 800;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    flex-shrink: 0;
}

.empty-threads-state {
    text-align: center;
    padding: 40px 16px;
    color: #94a3b8;
}

.empty-threads-state i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* ==================== 2. MAIN CHAT VIEWPORT ==================== */
.chat-viewport-pane {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    background: #f8fafc;
    position: relative;
    overflow: hidden;
}

.active-chat-header {
    padding: 12px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.header-student-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mobile-return-btn {
    display: none;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e3a8a;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    align-items: center;
    justify-content: center;
}

.header-avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 1.15rem;
    box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
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
    font-size: 0.74rem;
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

/* Canned Prompts Bar */
.teacher-canned-prompts-bar {
    padding: 8px 16px;
    background: #f1f5f9;
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
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
}

.canned-pill:hover {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #93c5fd;
    transform: translateY(-1px);
}

/* ==================== 3. MESSAGES FLOW CANVAS ==================== */
.messages-flow-canvas {
    flex: 1;
    min-height: 0;
    padding: 20px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Day Divider */
.chat-day-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 14px 0 10px;
    position: relative;
    text-align: center;
}

.chat-day-divider::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #e2e8f0;
    z-index: 1;
}

.chat-day-divider span {
    position: relative;
    z-index: 2;
    background: #ffffff;
    color: #475569;
    padding: 4px 14px;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Bubbles */
.bubble-item {
    max-width: 78%;
    padding: 10px 16px;
    border-radius: 14px;
    font-size: 0.93rem;
    line-height: 1.6;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    position: relative;
}

.teacher-sent {
    align-self: flex-end;
    background: #1e3a8a;
    color: #ffffff;
    border-top-left-radius: 3px;
}

.student-incoming {
    align-self: flex-start;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    border-top-right-radius: 3px;
}

.bubble-content-text {
    white-space: pre-wrap;
}

.msg-time-tag {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 6px;
    font-size: 0.72rem;
}

.teacher-sent .msg-time-tag {
    color: #bfdbfe;
}

.student-incoming .msg-time-tag {
    color: #64748b;
}

.read-check-icon {
    color: #67e8f9;
    font-size: 0.75rem;
}

.no-selection-placeholder {
    margin: auto;
    text-align: center;
    max-width: 440px;
    padding: 30px 16px;
    color: #64748b;
}

.placeholder-icon-circle {
    width: 68px;
    height: 68px;
    border-radius: 16px;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 2rem;
    margin: 0 auto 14px;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
}

.no-selection-placeholder h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}

/* ==================== 4. COMPOSER ==================== */
.teacher-composer-area {
    padding: 12px 18px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
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
    transition: all 0.15s ease;
    box-sizing: border-box;
}

.composer-textarea:focus {
    border-color: #1e3a8a;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
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
    transition: background 0.15s, transform 0.1s;
    flex-shrink: 0;
}

.composer-send-btn:hover {
    background: #0f172a;
}

.composer-send-btn:active {
    transform: scale(0.98);
}

.composer-hint-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    font-size: 0.72rem;
    color: #64748b;
}

/* ==================== 5. RESPONSIVE ==================== */
@media (max-width: 1024px) {
    .academic-inbox-grid {
        grid-template-columns: 300px 1fr;
    }
}

@media (max-width: 900px) {
    .academic-inbox-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100dvh !important;
    }
    .academic-inbox-grid {
        grid-template-columns: 1fr !important;
        height: 100dvh !important;
        min-height: 100dvh !important;
        max-height: 100dvh !important;
        border-radius: 0 !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
    }
    .chat-sidebar-pane.mobile-hidden { display: none !important; }
    .chat-viewport-pane.mobile-hidden { display: none !important; }
    .mobile-return-btn { display: inline-flex !important; }
    .bubble-item { max-width: 88% !important; }
    .chat-sidebar-pane, .chat-viewport-pane {
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
    }
    .teacher-composer-area {
        padding: 8px 12px !important;
        padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px)) !important;
    }
    .composer-textarea {
        font-size: 16px !important; /* يمنع التكبير التلقائي في Safari iOS */
    }
    .composer-hint-row {
        display: none !important;
    }
}
</style>

<script>
    let activeStudentId = null;
    let activeStudentName = "";
    let pollInterval = null;
    let lastContentHash = "";
    let soundEnabled = true;
    let currentFilter = 'all';

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

    function setStudentFilter(type) {
        currentFilter = type;
        document.querySelectorAll('.filter-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === type);
        });
        filterStudents();
    }

    function loadTeacherChat(id, name, stage) {
        activeStudentId = id;
        activeStudentName = name;
        lastContentHash = "";

        document.getElementById('chat_header').style.display = 'flex';
        document.getElementById('quick_teacher_prompts').style.display = 'flex';
        document.getElementById('input_area').style.display = 'block';
        const ph = document.getElementById('placeholderView');
        if (ph) ph.style.display = 'none';

        document.getElementById('active_user_name').innerText = name;
        document.getElementById('active_avatar').innerText = name.charAt(0);
        if (stage) {
            document.getElementById('active_stage_tag').innerText = stage;
        }

        // Active state on sidebar
        document.querySelectorAll('.student-thread-card').forEach(c => c.classList.remove('active'));
        const card = document.getElementById('user_' + id);
        if (card) {
            card.classList.add('active');
            // Reset unread badge on UI
            const badge = document.getElementById('badge_' + id);
            if (badge) {
                badge.style.display = 'none';
                card.setAttribute('data-unread', '0');
                card.classList.remove('has-unread');
            }
        }

        // Mobile / Tablet view toggle
        if (window.innerWidth <= 900) {
            const sidebar = document.getElementById('chat_sidebar');
            const main = document.getElementById('chat_main');
            if (sidebar) sidebar.classList.add('mobile-hidden');
            if (main) main.classList.remove('mobile-hidden');
        }

        fetchMessages(true);

        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3500);
    }

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('chat_sidebar');
        const main = document.getElementById('chat_main');
        if (sidebar) sidebar.classList.remove('mobile-hidden');
        if (main) main.classList.add('mobile-hidden');
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

                const isNearBottom = box.scrollHeight - box.scrollTop <= box.clientHeight + 140;
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
                let lastDate = "";

                messages.forEach(m => {
                    const msgDate = m.created_date || "";
                    const msgDateHuman = m.created_date_human || msgDate;

                    if (msgDate && msgDate !== lastDate) {
                        const divider = document.createElement('div');
                        divider.className = 'chat-day-divider';
                        divider.innerHTML = `<span><i class="fa-regular fa-calendar-days"></i> ${escapeHtml(msgDateHuman)}</span>`;
                        box.appendChild(divider);
                        lastDate = msgDate;
                    }

                    const sender = (m.sender_type || '').toLowerCase().trim();
                    const isMe = (sender === 'teacher');
                    const time = m.created_at_formatted || 'الآن';

                    const item = document.createElement('div');
                    item.className = `bubble-item ${isMe ? 'teacher-sent' : 'student-incoming'}`;
                    item.innerHTML = `
                        <div class="bubble-content-text">${escapeHtml(m.message)}</div>
                        <div class="msg-time-tag">
                            <span>${isMe ? '{{ __("أنت (المعلم)") }}' : escapeHtml(activeStudentName || '{{ __("الطالب") }}')}</span>
                            <span>•</span>
                            <span>${time}</span>
                            ${isMe ? '<i class="fa-solid fa-check-double read-check-icon"></i>' : ''}
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
            <div class="bubble-content-text">${escapeHtml(text)}</div>
            <div class="msg-time-tag">
                <span>{{ __("أنت (المعلم)") }}</span>
                <span>•</span>
                <span>الآن</span>
                <i class="fa-solid fa-check text-white-50"></i>
            </div>
        `;
        box.appendChild(optimisticBubble);
        scrollToBottomSmooth();

        // Update sidebar card preview
        const snippet = document.getElementById('snippet_' + activeStudentId);
        if (snippet) {
            snippet.innerHTML = `<strong class="me-prefix">{{ __('أنت') }}: </strong>` + escapeHtml(text.substring(0, 32));
        }
        const timeEl = document.getElementById('time_' + activeStudentId);
        if (timeEl) {
            timeEl.innerText = 'الآن';
        }

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
            input.focus({ preventScroll: true });
        });
    }

    function insertTeacherCanned(text) {
        const input = document.getElementById('msg_input');
        input.value = text;
        autoExpandTextarea(input);
        input.focus({ preventScroll: true });
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
        if (box) {
            setTimeout(() => {
                box.scrollTop = box.scrollHeight;
            }, 50);
        }
    }

    function filterStudents() {
        const query = document.getElementById('search_student').value.toLowerCase().trim();
        const clearBtn = document.getElementById('clear_search_btn');
        if (clearBtn) clearBtn.style.display = query.length > 0 ? 'block' : 'none';

        const cards = document.querySelectorAll('.student-thread-card');
        let visibleCount = 0;

        cards.forEach(c => {
            const data = c.getAttribute('data-search') || '';
            const isUnread = c.getAttribute('data-unread') === '1';

            let matchesSearch = data.includes(query);
            let matchesFilter = true;

            if (currentFilter === 'unread') {
                matchesFilter = isUnread;
            }

            if (matchesSearch && matchesFilter) {
                c.style.display = 'flex';
                visibleCount++;
            } else {
                c.style.display = 'none';
            }
        });

        const noRes = document.getElementById('no_results');
        if (noRes) {
            noRes.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }

    function clearSearch() {
        const input = document.getElementById('search_student');
        if (input) {
            input.value = '';
            filterStudents();
            input.focus();
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const sid = urlParams.get('student_id') || '{{ $selectedStudentId ?? "" }}';
        if (sid) {
            const card = document.getElementById('user_' + sid);
            if (card) {
                card.click();
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                @if(isset($selectedStudent) && $selectedStudent)
                    loadTeacherChat(
                        {{ $selectedStudent->id }},
                        '{{ addslashes($selectedStudent->name_ar ?? $selectedStudent->name ?? "طالب") }}',
                        '{{ addslashes($selectedStudent->stage ? ($selectedStudent->stage->label_ar ?? $selectedStudent->stage->name_ar ?? "توجيهي") : "توجيهي") }}'
                    );
                @endif
            }
        }
    });
</script>
@endsection
