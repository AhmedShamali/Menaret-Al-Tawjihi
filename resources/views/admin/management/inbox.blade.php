@extends('layouts.app')

@section('title', 'مركز المحادثات والتواصل الأكاديمي | ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="inbox-classic-wrapper">
    <!-- Main Chat Container -->
    <div class="inbox-classic-card" id="chat_container">
        
        <!-- ==================== SIDEBAR: CONVERSATION LIST ==================== -->
        <aside class="inbox-classic-sidebar" id="sidebar_view">
            <!-- Sidebar Header -->
            <div class="sidebar-top-pane">
                <div class="sidebar-headline-row">
                    <div class="brand-title-group">
                        <div class="brand-icon-box">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <h2 class="brand-title">المراسلات والدعم</h2>
                            <p class="brand-desc">التواصل المباشر مع الطلاب</p>
                        </div>
                    </div>
                    @php
                        $totalUnread = $chats->sum('unread_count');
                    @endphp
                    @if($totalUnread > 0)
                        <span class="total-unread-badge" id="global_unread_badge">
                            {{ $totalUnread }} جديدة
                        </span>
                    @endif
                </div>

                <!-- Search Input -->
                <div class="sidebar-search-box">
                    <i class="fa-solid fa-magnifying-glass search-ico"></i>
                    <input type="text" id="search_student" oninput="filterStudents()" placeholder="بحث بالاسم أو الهاتف أو التخصص..." autocomplete="off">
                    <button type="button" id="clear_search" class="clear-btn" onclick="clearSearch()" style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Tabs Filter -->
                <div class="sidebar-filter-tabs">
                    <button type="button" class="filter-chip active" data-filter="all" onclick="setFilter('all')">
                        الكل <span class="chip-count">({{ count($chats) }})</span>
                    </button>
                    <button type="button" class="filter-chip" data-filter="unread" onclick="setFilter('unread')">
                        غير مقروءة <span class="chip-count" id="tab_unread_count">({{ $totalUnread }})</span>
                    </button>
                </div>
            </div>

            <!-- Student List -->
            <div class="student-scroll-list" id="student_list">
                @forelse($chats as $student)
                    @php
                        $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'طالب';
                        $firstLetter = mb_substr($studentName, 0, 1);
                        $stageName = $student->stage ? ($student->stage->name_ar ?? $student->stage->name ?? 'توجيهي') : 'توجيهي';
                        $phone = $student->phone ?? $student->whatsapp ?? '';
                        $hasUnread = ($student->unread_count ?? 0) > 0;
                        $lastText = $student->last_message ?? 'محادثة جديدة';
                        $lastTime = $student->last_message_time ?? '';
                        $isMe = ($student->last_sender_type === 'admin');
                    @endphp
                    <div onclick="loadChat({{ $student->id }}, '{{ addslashes($studentName) }}', '{{ addslashes($stageName) }}', '{{ addslashes($phone) }}', '{{ $student->status }}')"
                         class="student-chat-item {{ $hasUnread ? 'unread' : '' }} {{ (isset($selectedStudentId) && $selectedStudentId == $student->id) ? 'active' : '' }}"
                         id="user_{{ $student->id }}"
                         data-student-id="{{ $student->id }}"
                         data-unread="{{ $hasUnread ? '1' : '0' }}"
                         data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '') . ' ' . $phone . ' ' . $stageName) }}">
                        
                        <div class="item-avatar-wrapper">
                            @if(!empty($student->photo))
                                <img src="{{ asset('storage/' . $student->photo) }}" 
                                     alt="" 
                                     class="item-avatar-img"
                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="item-avatar-letter fallback-letter" style="display: none;">{{ $firstLetter }}</div>
                            @else
                                <div class="item-avatar-letter">{{ $firstLetter }}</div>
                            @endif
                            <span class="online-dot {{ $student->status === 'active' ? 'online' : 'offline' }}"></span>
                        </div>

                        <div class="item-content-pane">
                            <div class="item-header-line">
                                <span class="item-name" title="{{ $studentName }}">{{ $studentName }}</span>
                                <span class="item-time" id="time_{{ $student->id }}">{{ $lastTime }}</span>
                            </div>
                            
                            <div class="item-stage-line">
                                <span class="stage-tag">{{ $stageName }}</span>
                            </div>

                            <div class="item-snippet-line">
                                <span class="snippet-text" id="snippet_{{ $student->id }}">
                                    @if($student->last_message)
                                        @if($isMe)<strong class="me-prefix">أنت: </strong>@endif{{ Str::limit($lastText, 32) }}
                                    @else
                                        <span class="new-conversation-hint">انقر لبدء المحادثة</span>
                                    @endif
                                </span>
                                <span class="unread-counter-pill" id="badge_{{ $student->id }}" style="{{ $hasUnread ? '' : 'display: none;' }}">
                                    {{ $student->unread_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-list-box">
                        <i class="fa-regular fa-comments"></i>
                        <p>لا يوجد طلاب مسجلين حالياً</p>
                    </div>
                @endforelse

                <div id="no_results" class="empty-list-box" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>لم يتم العثور على محادثات مطابقة</p>
                </div>
            </div>
        </aside>

        <!-- ==================== MAIN CHAT VIEWPORT ==================== -->
        <main class="inbox-classic-main" id="chat_view">
            
            <!-- Empty State -->
            <div id="chat_empty_state" class="empty-conversation-state" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: none;' : 'display: flex;' }}">
                <div class="empty-icon-circle">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3>مركز المراسلات والتواصل الأكاديمي</h3>
                <p>اختر طالباً من القائمة الجانبية لعرض المحادثة والرد على استفساراته وتقديم الدعم الدراسي.</p>
                <div class="academic-features-row">
                    <div class="feat-box">
                        <i class="fa-solid fa-bolt"></i>
                        <span>ردود جاهزة معتمدة</span>
                    </div>
                    <div class="feat-box">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>تحويل مباشر للواتساب</span>
                    </div>
                    <div class="feat-box">
                        <i class="fa-solid fa-bell"></i>
                        <span>إشعارات أكاديمية فورية</span>
                    </div>
                </div>
            </div>

            <!-- Active Header -->
            <header id="chat_header" class="active-conversation-header" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="header-profile-cluster">
                    <button type="button" class="mobile-return-btn" onclick="toggleMobileView('sidebar')" title="العودة للقائمة">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    
                    <div class="header-avatar" id="active_avatar">
                        {{ isset($selectedStudent) ? mb_substr($selectedStudent->name_ar ?? $selectedStudent->name ?? 'ط', 0, 1) : 'ط' }}
                    </div>
                    
                    <div class="header-meta-details">
                        <div class="title-and-stage">
                            <h3 id="active_user_name" class="active-student-name">
                                {{ isset($selectedStudent) ? ($selectedStudent->name_ar ?? $selectedStudent->name ?? 'طالب') : 'اختر طالباً' }}
                            </h3>
                            <span id="active_stage_tag" class="student-stage-pill">
                                {{ isset($selectedStudent) && $selectedStudent->stage ? ($selectedStudent->stage->name_ar ?? $selectedStudent->stage->name ?? 'توجيهي') : 'توجيهي' }}
                            </span>
                        </div>
                        <div class="status-and-contact">
                            <span class="status-indicator">
                                <span class="pulse-emerald"></span> مسجل في المنصة
                            </span>
                            <span class="meta-divider">•</span>
                            <span id="active_phone_display" class="contact-pill">
                                <i class="fa-solid fa-phone"></i>
                                <span id="active_phone_number">{{ isset($selectedStudent) ? ($selectedStudent->phone ?? $selectedStudent->whatsapp ?? 'غير محدد') : '---' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="header-controls">
                    <!-- WhatsApp -->
                    <a href="#" id="btn_whatsapp_direct" target="_blank" class="control-btn btn-wa" style="display: none;" title="محادثة عبر واتساب">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>واتساب</span>
                    </a>
                    
                    <!-- Profile -->
                    <a href="#" id="btn_view_profile" target="_blank" class="control-btn btn-academic" style="display: none;" title="عرض الملف الأكاديمي">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>الملف الأكاديمي</span>
                    </a>

                    <!-- Refresh -->
                    <button type="button" class="control-btn btn-icon" onclick="fetchMessages(true)" title="تحديث الرسائل">
                        <i class="fa-solid fa-rotate-right" id="refresh_icon"></i>
                    </button>
                </div>
            </header>

            <!-- Messages Stream Area -->
            <div id="chat_messages" class="messages-canvas" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="loading-state-box">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span>جاري مزامنة المحادثة...</span>
                </div>
            </div>

            <!-- Quick Reply Prompts Toolbar -->
            <div id="quick_replies_bar" class="canned-responses-pane" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="canned-label">
                    <i class="fa-solid fa-bolt"></i>
                    <span>ردود سريعة:</span>
                </div>
                <div class="canned-pills-flow">
                    <button type="button" class="canned-chip" onclick="insertCanned('وعليكم السلام ورحمة الله، أهلاً بك! كيف يمكننا مساعدتك؟')">
                        👋 ترحيب
                    </button>
                    <button type="button" class="canned-chip" onclick="insertCanned('تم التحقق وتفعيل حسابك واشتراكك بنجاح، بالتوفيق!')">
                        ✅ تم تفعيل الاشتراك
                    </button>
                    <button type="button" class="canned-chip" onclick="insertCanned('يرجى تزويدنا بصورة إيصال السداد أو رقم الحوالة المالية لمطابقتها.')">
                        📄 طلب إيصال سداد
                    </button>
                    <button type="button" class="canned-chip" onclick="insertCanned('تم تحويل استفسارك إلى مدرس المادة، وسيقوم بالإجابة عليك قريباً.')">
                        👨‍🏫 إحالة للمعلم
                    </button>
                    <button type="button" class="canned-chip" onclick="insertCanned('تم حل المشكلة وتحديث البيانات في حسابك بنجاح.')">
                        ✨ تم حل المشكلة
                    </button>
                </div>
            </div>

            <!-- Message Input Area -->
            <div id="input_area" class="composer-container" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: block;' : 'display: none;' }}">
                <form id="chatForm" onsubmit="event.preventDefault(); sendReply();" class="composer-form">
                    <div class="composer-box">
                        <textarea id="msg_input" 
                                  rows="1" 
                                  placeholder="اكتب ردك الأكاديمي هنا... (اضغط Enter للإرسال، Shift+Enter لسطر جديد)" 
                                  autocomplete="off" 
                                  required></textarea>
                        
                        <button type="submit" class="composer-send-btn" id="btn_send" title="إرسال">
                            <span>إرسال</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<style>
    /* =========================================================
       CLASSIC ROYAL INBOX STYLING (Prestige Academic Design)
       ========================================================= */
    :root {
        --cr-navy-dark: #0f172a;
        --cr-navy-main: #1e3a8a;
        --cr-navy-light: #2563eb;
        --cr-navy-soft: #eff6ff;
        --cr-bg: #f8fafc;
        --cr-surface: #ffffff;
        --cr-border: #e2e8f0;
        --cr-border-subtle: #f1f5f9;
        --cr-text-primary: #0f172a;
        --cr-text-muted: #64748b;
        --cr-emerald: #10b981;
        --cr-danger: #ef4444;
        --cr-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08);
        --cr-radius-card: 16px;
        --cr-radius-bubble: 14px;
    }

    .inbox-classic-wrapper {
        padding: 0 0 12px 0;
        max-width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }

    .inbox-classic-card {
        display: grid;
        grid-template-columns: 350px 1fr;
        height: calc(100vh - 105px);
        max-height: calc(100vh - 105px);
        min-height: 520px;
        background: var(--cr-surface);
        border: 1px solid var(--cr-border);
        border-radius: var(--cr-radius-card);
        overflow: hidden;
        box-shadow: var(--cr-shadow);
    }

    /* -----------------------------------
       SIDEBAR: CONVERSATION LIST
       ----------------------------------- */
    .inbox-classic-sidebar {
        display: flex;
        flex-direction: column;
        background: var(--cr-surface);
        border-left: 1px solid var(--cr-border);
        height: 100%;
        min-width: 0;
        overflow: hidden;
    }

    html[dir="ltr"] .inbox-classic-sidebar {
        border-left: none;
        border-right: 1px solid var(--cr-border);
    }

    .sidebar-top-pane {
        padding: 18px 18px 12px;
        background: var(--cr-surface);
        border-bottom: 1px solid var(--cr-border);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .sidebar-headline-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .brand-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eff6ff;
        color: var(--cr-navy-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 1px solid #dbeafe;
    }

    .brand-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--cr-text-primary);
        margin: 0;
        line-height: 1.2;
    }

    .brand-desc {
        font-size: 0.74rem;
        color: var(--cr-text-muted);
        margin: 0;
    }

    .total-unread-badge {
        background: var(--cr-danger);
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
    }

    .sidebar-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sidebar-search-box .search-ico {
        position: absolute;
        right: 14px;
        color: #94a3b8;
        font-size: 0.85rem;
        pointer-events: none;
    }

    .sidebar-search-box input {
        width: 100%;
        padding: 9px 38px 9px 34px;
        background: #f8fafc;
        border: 1px solid var(--cr-border);
        border-radius: 10px;
        font-size: 0.84rem;
        color: var(--cr-text-primary);
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
    }

    .sidebar-search-box input:focus {
        background: #ffffff;
        border-color: var(--cr-navy-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .clear-btn {
        position: absolute;
        left: 10px;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 0.8rem;
    }

    .clear-btn:hover {
        color: var(--cr-text-primary);
    }

    .sidebar-filter-tabs {
        display: flex;
        gap: 8px;
    }

    .filter-chip {
        flex: 1;
        background: #f1f5f9;
        border: 1px solid var(--cr-border);
        color: var(--cr-text-muted);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
    }

    .filter-chip:hover {
        background: #e2e8f0;
        color: var(--cr-text-primary);
    }

    .filter-chip.active {
        background: var(--cr-navy-main);
        color: #ffffff;
        border-color: var(--cr-navy-main);
    }

    .student-scroll-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 10px;
    }

    /* Individual Student Card */
    .student-chat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        border: 1px solid transparent;
        background: #ffffff;
        position: relative;
        overflow: hidden;
        text-align: right;
    }

    html[dir="ltr"] .student-chat-item {
        text-align: left;
    }

    .student-chat-item:hover {
        background: #f8fafc;
        border-color: var(--cr-border);
    }

    .student-chat-item.active {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .student-chat-item.active::before {
        content: '';
        position: absolute;
        right: 0;
        top: 8px;
        bottom: 8px;
        width: 4px;
        background: var(--cr-navy-main);
        border-radius: 4px 0 0 4px;
    }

    html[dir="ltr"] .student-chat-item.active::before {
        right: auto;
        left: 0;
        border-radius: 0 4px 4px 0;
    }

    .item-avatar-wrapper {
        position: relative;
        width: 44px;
        height: 44px;
        min-width: 44px;
        max-width: 44px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-avatar-letter {
        width: 44px;
        height: 44px;
        min-width: 44px;
        max-width: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.05rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        user-select: none;
    }

    .item-avatar-img {
        width: 44px;
        height: 44px;
        min-width: 44px;
        max-width: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--cr-border);
        display: block;
        background: #f1f5f9;
    }

    .online-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    html[dir="ltr"] .online-dot {
        right: auto;
        left: 0;
    }

    .online-dot.online {
        background: var(--cr-emerald);
    }

    .online-dot.offline {
        background: #94a3b8;
    }

    .item-content-pane {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: right;
    }

    html[dir="ltr"] .item-content-pane {
        text-align: left;
    }

    .item-header-line {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 6px;
        width: 100%;
    }

    .item-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--cr-text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        text-align: right;
    }

    html[dir="ltr"] .item-name {
        text-align: left;
    }

    .item-time {
        font-size: 0.68rem;
        color: var(--cr-text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .item-stage-line {
        display: flex;
        align-items: center;
        width: 100%;
        margin-top: 1px;
    }

    .stage-tag {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        display: inline-block;
    }

    .student-chat-item.active .stage-tag {
        background: #dbeafe;
        color: #1e40af;
    }

    .item-snippet-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
        width: 100%;
    }

    .snippet-text {
        font-size: 0.76rem;
        color: var(--cr-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        text-align: right;
    }

    html[dir="ltr"] .snippet-text {
        text-align: left;
    }

    .me-prefix {
        font-weight: 700;
        color: #334155;
    }

    .new-conversation-hint {
        font-style: italic;
        opacity: 0.7;
    }

    .unread-counter-pill {
        background: var(--cr-danger);
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 1px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }

    .empty-list-box {
        padding: 40px 16px;
        text-align: center;
        color: var(--cr-text-muted);
    }

    .empty-list-box i {
        font-size: 2rem;
        color: #cbd5e1;
        margin-bottom: 8px;
        display: block;
    }

    .empty-list-box p {
        font-size: 0.84rem;
        margin: 0;
    }

    /* -----------------------------------
       MAIN CHAT PANE
       ----------------------------------- */
    .inbox-classic-main {
        display: flex;
        flex-direction: column;
        background: var(--cr-bg);
        height: 100%;
        min-width: 0;
        position: relative;
    }

    /* Empty state */
    .empty-conversation-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 24px;
        text-align: center;
    }

    .empty-icon-circle {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.25);
    }

    .empty-conversation-state h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--cr-text-primary);
        margin: 0 0 8px;
    }

    .empty-conversation-state p {
        font-size: 0.88rem;
        color: var(--cr-text-muted);
        max-width: 420px;
        line-height: 1.6;
        margin: 0 0 24px;
    }

    .academic-features-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 360px;
        width: 100%;
    }

    .feat-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid var(--cr-border);
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        color: #334155;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .feat-box i {
        color: var(--cr-navy-main);
        font-size: 1rem;
    }

    /* Active Header */
    .active-conversation-header {
        padding: 12px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--cr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        z-index: 5;
    }

    .header-profile-cluster {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .mobile-return-btn {
        display: none;
        background: none;
        border: none;
        color: var(--cr-text-primary);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 6px;
    }

    .header-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }

    .header-meta-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .title-and-stage {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-student-name {
        font-size: 1rem;
        font-weight: 800;
        color: var(--cr-text-primary);
        margin: 0;
    }

    .student-stage-pill {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #dbeafe;
    }

    .status-and-contact {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.74rem;
        color: var(--cr-text-muted);
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .pulse-emerald {
        width: 7px;
        height: 7px;
        background: var(--cr-emerald);
        border-radius: 50%;
        display: inline-block;
    }

    .meta-divider {
        color: #cbd5e1;
    }

    .contact-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .contact-pill i {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .header-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .control-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1px solid var(--cr-border);
        background: #ffffff;
        color: var(--cr-text-primary);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .control-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .btn-wa {
        background: #25d366;
        color: #ffffff;
        border-color: #22c55e;
    }

    .btn-wa:hover {
        background: #16a34a;
        color: #ffffff;
    }

    .btn-academic {
        background: #eff6ff;
        color: var(--cr-navy-main);
        border-color: #bfdbfe;
    }

    .btn-academic:hover {
        background: #dbeafe;
    }

    .btn-icon {
        padding: 7px 10px;
    }

    /* Messages Canvas: Serene & Classic (NO UGLY DOTS!) */
    .messages-canvas {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        background: #f8fafc;
    }

    .loading-state-box {
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: var(--cr-text-muted);
        font-size: 0.88rem;
    }

    .empty-chat-state {
        margin: auto;
        text-align: center;
        color: var(--cr-text-muted);
        padding: 24px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px dashed var(--cr-border);
        max-width: 360px;
    }

    .empty-chat-state i {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 8px;
        display: block;
    }

    .empty-chat-state h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--cr-text-primary);
        margin: 0 0 4px;
    }

    .empty-chat-state p {
        font-size: 0.8rem;
        margin: 0;
    }

    /* Classic Bubble Rows */
    .bubble-row {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        width: 100%;
    }

    .bubble-row.is-student {
        justify-content: flex-start;
        flex-direction: row;
    }

    .bubble-row.is-admin {
        justify-content: flex-end;
        flex-direction: row-reverse;
    }

    .bubble-avatar-mini {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .bubble-row.is-student .bubble-avatar-mini {
        background: #e2e8f0;
        color: #475569;
    }

    .bubble-row.is-admin .bubble-avatar-mini {
        background: var(--cr-navy-main);
        color: #ffffff;
    }

    .bubble-card {
        max-width: 65%;
        min-width: 140px;
        padding: 12px 16px;
        font-size: 0.9rem;
        line-height: 1.6;
        word-break: break-word;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        animation: bubbleSlideIn 0.2s ease-out;
    }

    @keyframes bubbleSlideIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bubble-row.is-admin .bubble-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #ffffff;
        border-radius: 14px 14px 4px 14px;
    }

    .bubble-row.is-student .bubble-card {
        background: #ffffff;
        color: var(--cr-text-primary);
        border: 1px solid var(--cr-border);
        border-radius: 14px 14px 14px 4px;
    }

    .bubble-sender-name {
        font-size: 0.72rem;
        font-weight: 800;
        margin-bottom: 4px;
        display: block;
    }

    .bubble-row.is-admin .bubble-sender-name {
        color: #93c5fd;
    }

    .bubble-row.is-student .bubble-sender-name {
        color: var(--cr-navy-main);
    }

    .bubble-message-text {
        white-space: pre-wrap;
    }

    .bubble-footer-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        margin-top: 6px;
        font-size: 0.68rem;
    }

    .bubble-row.is-admin .bubble-footer-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    .bubble-row.is-student .bubble-footer-meta {
        color: var(--cr-text-muted);
    }

    .bubble-footer-meta i {
        font-size: 0.7rem;
    }

    /* Quick Reply Bar */
    .canned-responses-pane {
        background: #ffffff;
        border-top: 1px solid var(--cr-border);
        padding: 9px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .canned-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--cr-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .canned-label i {
        color: #d97706;
    }

    .canned-pills-flow {
        display: flex;
        gap: 8px;
        overflow-x: auto;
    }

    .canned-chip {
        background: #f8fafc;
        border: 1px solid var(--cr-border);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.78rem;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-family: inherit;
        font-weight: 600;
    }

    .canned-chip:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: var(--cr-navy-main);
    }

    /* Input Composer */
    .composer-container {
        padding: 12px 18px;
        background: #ffffff;
        border-top: 1px solid var(--cr-border);
    }

    .composer-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid var(--cr-border);
        border-radius: 12px;
        padding: 6px 8px 6px 12px;
        transition: all 0.2s ease;
    }

    .composer-box:focus-within {
        background: #ffffff;
        border-color: var(--cr-navy-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .composer-box textarea {
        flex: 1;
        border: none;
        background: transparent;
        resize: none;
        outline: none;
        font-family: inherit;
        font-size: 0.9rem;
        line-height: 1.5;
        max-height: 120px;
        padding: 6px 0;
        color: var(--cr-text-primary);
    }

    .composer-send-btn {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #ffffff;
        border: none;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25);
    }

    .composer-send-btn:hover {
        background: #172554;
        box-shadow: 0 4px 10px rgba(30, 58, 138, 0.35);
    }

    .composer-send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Scrollbars */
    .student-scroll-list::-webkit-scrollbar,
    .messages-canvas::-webkit-scrollbar,
    .canned-pills-flow::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .student-scroll-list::-webkit-scrollbar-thumb,
    .messages-canvas::-webkit-scrollbar-thumb,
    .canned-pills-flow::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .inbox-classic-wrapper {
            padding: 8px;
        }
        .inbox-classic-card {
            grid-template-columns: 1fr;
            height: calc(100vh - 100px);
            border-radius: 12px;
        }
        .inbox-classic-main {
            display: none;
        }
        .inbox-classic-card.mobile-active .inbox-classic-sidebar {
            display: none;
        }
        .inbox-classic-card.mobile-active .inbox-classic-main {
            display: flex;
        }
        .mobile-return-btn {
            display: inline-block;
        }
        .bubble-card {
            max-width: 85%;
        }
        .composer-send-btn span {
            display: none;
        }
    }
</style>

<script>
    let activeStudentId = null;
    let pollInterval = null;
    let lastMessagesJson = "";
    let activeFilter = 'all';

    const sendUrl = "{{ route('admin.send') }}";
    const fetchUrlBase = "{{ url('/admin/fetch') }}";
    const profileUrlBase = "{{ url('/admin/students') }}";

    function toggleMobileView(view) {
        const container = document.getElementById('chat_container');
        if (view === 'chat') {
            container.classList.add('mobile-active');
        } else {
            container.classList.remove('mobile-active');
            if (pollInterval) clearInterval(pollInterval);
            activeStudentId = null;
        }
    }

    function setFilter(filter) {
        activeFilter = filter;
        document.querySelectorAll('.filter-chip').forEach(b => {
            b.classList.toggle('active', b.dataset.filter === filter);
        });
        filterStudents();
    }

    function filterStudents() {
        const query = document.getElementById('search_student').value.toLowerCase().trim();
        const clearBtn = document.getElementById('clear_search');
        if (clearBtn) {
            clearBtn.style.display = query ? 'block' : 'none';
        }

        const cards = document.querySelectorAll('.student-chat-item');
        let visibleCount = 0;

        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            const isUnread = card.getAttribute('data-unread') === '1';

            const matchesQuery = query === '' || searchData.includes(query);
            const matchesFilter = (activeFilter === 'all') || (activeFilter === 'unread' && isUnread);

            if (matchesQuery && matchesFilter) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('no_results');
        if (noResults) {
            noResults.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }

    function clearSearch() {
        const input = document.getElementById('search_student');
        input.value = '';
        filterStudents();
        input.focus();
    }

    function loadChat(id, name, stage, phone, status) {
        activeStudentId = id;
        lastMessagesJson = "";

        // Update URL
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('student_id', id);
        window.history.replaceState({ path: newUrl.href }, '', newUrl.href);

        toggleMobileView('chat');

        // Show chat interface elements
        document.getElementById('chat_empty_state').style.display = 'none';
        document.getElementById('chat_header').style.display = 'flex';
        document.getElementById('quick_replies_bar').style.display = 'flex';
        document.getElementById('input_area').style.display = 'block';
        document.getElementById('chat_messages').style.display = 'flex';

        // Update header details
        document.getElementById('active_user_name').innerText = name || 'طالب';
        document.getElementById('active_avatar').innerText = (name || 'ط').charAt(0);
        
        if (stage) {
            document.getElementById('active_stage_tag').innerText = stage;
            document.getElementById('active_stage_tag').style.display = 'inline-block';
        }

        const phoneDisplay = document.getElementById('active_phone_number');
        const waBtn = document.getElementById('btn_whatsapp_direct');
        if (phone && phone.trim() !== '') {
            phoneDisplay.innerText = phone;
            document.getElementById('active_phone_display').style.display = 'inline-flex';
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            waBtn.href = 'https://wa.me/' + cleanPhone;
            waBtn.style.display = 'inline-flex';
        } else {
            phoneDisplay.innerText = 'لا يوجد رقم';
            waBtn.style.display = 'none';
        }

        const profileBtn = document.getElementById('btn_view_profile');
        profileBtn.href = profileUrlBase + '/' + id;
        profileBtn.style.display = 'inline-flex';

        // Highlight active card
        document.querySelectorAll('.student-chat-item').forEach(c => c.classList.remove('active'));
        const activeCard = document.getElementById('user_' + id);
        if (activeCard) {
            activeCard.classList.add('active');
            const badge = document.getElementById('badge_' + id);
            if (badge) {
                badge.style.display = 'none';
            }
            activeCard.setAttribute('data-unread', '0');
        }

        // Show loading in messages area
        const box = document.getElementById('chat_messages');
        box.innerHTML = `
            <div class="loading-state-box">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>جاري مزامنة المحادثة...</span>
            </div>
        `;

        // Fetch messages immediately
        fetchMessages(true);

        // Auto-refresh poll every 3.5 seconds
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3500);

        setTimeout(() => {
            const input = document.getElementById('msg_input');
            if (input) input.focus();
        }, 150);
    }

    function fetchMessages(isFirstLoad = false) {
        if (!activeStudentId) return;

        const refreshIcon = document.getElementById('refresh_icon');
        if (isFirstLoad && refreshIcon) {
            refreshIcon.classList.add('fa-spin');
        }

        axios.get(fetchUrlBase + '/' + activeStudentId)
            .then(res => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                
                const box = document.getElementById('chat_messages');
                const messages = res.data.messages || [];

                if (res.data.student) {
                    const st = res.data.student;
                    if (st.name) {
                        document.getElementById('active_user_name').innerText = st.name;
                        document.getElementById('active_avatar').innerText = st.name.charAt(0);
                    }
                    if (st.stage) {
                        document.getElementById('active_stage_tag').innerText = st.stage;
                    }
                    if (st.phone && st.phone.trim() !== '') {
                        const cleanPhone = st.phone.replace(/[^0-9]/g, '');
                        const waBtn = document.getElementById('btn_whatsapp_direct');
                        waBtn.href = 'https://wa.me/' + cleanPhone;
                        waBtn.style.display = 'inline-flex';
                        document.getElementById('active_phone_number').innerText = st.phone;
                        document.getElementById('active_phone_display').style.display = 'inline-flex';
                    }
                }

                const currentJson = JSON.stringify(messages);
                if (currentJson === lastMessagesJson && !isFirstLoad) {
                    return;
                }
                lastMessagesJson = currentJson;

                if (messages.length === 0) {
                    box.innerHTML = `
                        <div class="empty-chat-state">
                            <i class="fa-regular fa-comment-dots"></i>
                            <h4>لا توجد رسائل سابقة</h4>
                            <p>ابدأ المحادثة مع الطالب عبر كتابة رسالة بالأسفل.</p>
                        </div>
                    `;
                    return;
                }

                box.innerHTML = '';
                messages.forEach(m => appendMessage(m));

                if (isFirstLoad || isScrolledToBottom(box)) {
                    setTimeout(scrollToBottom, 60);
                }
            })
            .catch(err => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                console.error("خطأ في جلب الرسائل:", err);
            });
    }

    function appendMessage(msg) {
        const box = document.getElementById('chat_messages');
        const emptyMsg = box.querySelector('.empty-chat-state');
        if (emptyMsg) emptyMsg.remove();

        const sender = (msg.sender_type || '').toLowerCase().trim();
        const isAdmin = (sender === 'admin');
        const time = msg.created_at_formatted || '';
        const studentName = document.getElementById('active_user_name').innerText;
        const authorLabel = isAdmin ? 'إدارة المنصة' : studentName;
        const avatarLetter = isAdmin ? '<i class="fa-solid fa-user-shield"></i>' : studentName.charAt(0);

        const row = document.createElement('div');
        row.className = `bubble-row ${isAdmin ? 'is-admin' : 'is-student'}`;
        row.innerHTML = `
            <div class="bubble-avatar-mini" title="${escapeHtml(authorLabel)}">
                ${avatarLetter}
            </div>
            <div class="bubble-card">
                <span class="bubble-sender-name">${escapeHtml(authorLabel)}</span>
                <div class="bubble-message-text">${escapeHtml(msg.message)}</div>
                <div class="bubble-footer-meta">
                    <span>${escapeHtml(time)}</span>
                    ${isAdmin ? '<i class="fa-solid fa-check-double text-info"></i>' : ''}
                </div>
            </div>
        `;
        box.appendChild(row);
    }

    function sendReply() {
        const input = document.getElementById('msg_input');
        const message = input.value.trim();
        const sendBtn = document.getElementById('btn_send');

        if (!message || !activeStudentId) return;

        sendBtn.disabled = true;
        const originalContent = sendBtn.innerHTML;
        sendBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        axios.post(sendUrl, {
            student_id: activeStudentId,
            message: message
        }, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(res => {
            input.value = '';
            input.style.height = 'auto';
            lastMessagesJson = "";
            fetchMessages(true);

            // Update snippet in sidebar
            const snippet = document.getElementById('snippet_' + activeStudentId);
            if (snippet) {
                snippet.innerHTML = `<strong class="me-prefix">أنت: </strong>` + escapeHtml(message.substring(0, 32));
            }
            const timeEl = document.getElementById('time_' + activeStudentId);
            if (timeEl) {
                timeEl.innerText = 'الآن';
            }
        }).catch(err => {
            console.error('Error sending message:', err);
            const msg = err.response?.data?.message || 'تعذر إرسال الرسالة، يرجى المحاولة مرة أخرى.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'تنبيه',
                    text: msg,
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert(msg);
            }
        }).finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalContent;
            input.focus();
        });
    }

    function insertCanned(text) {
        const input = document.getElementById('msg_input');
        input.value = text;
        input.focus();
    }

    function scrollToBottom() {
        const box = document.getElementById('chat_messages');
        if (box) box.scrollTop = box.scrollHeight;
    }

    function isScrolledToBottom(box) {
        return (box.scrollHeight - box.clientHeight) <= (box.scrollTop + 160);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('msg_input');
        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendReply();
                }
            });
        }

        // Auto-select student from URL param ?student_id=XX
        const urlParams = new URLSearchParams(window.location.search);
        const paramStudentId = urlParams.get('student_id') || '{{ $selectedStudentId ?? "" }}';

        if (paramStudentId) {
            const targetCard = document.getElementById('user_' + paramStudentId);
            if (targetCard) {
                targetCard.click();
                const list = document.getElementById('student_list');
                if (list) {
                    list.scrollTop = targetCard.offsetTop - list.offsetTop - 12;
                }
            } else {
                @if(isset($selectedStudent) && $selectedStudent)
                    loadChat(
                        {{ $selectedStudent->id }},
                        '{{ addslashes($selectedStudent->name_ar ?? $selectedStudent->name ?? "طالب") }}',
                        '{{ addslashes($selectedStudent->stage->name_ar ?? $selectedStudent->stage->name ?? "توجيهي") }}',
                        '{{ addslashes($selectedStudent->phone ?? $selectedStudent->whatsapp ?? "") }}',
                        '{{ $selectedStudent->status }}'
                    );
                @endif
            }
        }
        // Keep window pinned to the top without unwanted document scrolling
        window.scrollTo(0, 0);
    });
</script>
@endsection
