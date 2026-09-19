@extends('layouts.app')

@section('title', 'مركز المحادثات والتواصل الأكاديمي | ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="inbox-page-container">
    <!-- Main Chat Workspace Card -->
    <div class="inbox-card" id="chat_container">
        
        <!-- ==================== SIDEBAR: CONVERSATION LIST ==================== -->
        <aside class="inbox-sidebar" id="sidebar_view">
            <!-- Sidebar Header -->
            <div class="sidebar-head">
                <div class="head-title-row">
                    <div class="title-with-icon">
                        <span class="head-icon"><i class="fa-solid fa-comments"></i></span>
                        <div>
                            <h2 class="head-title">المراسلات والدعم</h2>
                            <p class="head-subtitle">التواصل المباشر مع الطلاب</p>
                        </div>
                    </div>
                    @php
                        $totalUnread = $chats->sum('unread_count');
                    @endphp
                    @if($totalUnread > 0)
                        <span class="badge-unread-total" id="global_unread_badge" title="رسائل غير مقروءة">
                            {{ $totalUnread }} جديدة
                        </span>
                    @endif
                </div>

                <!-- Search Box -->
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="search_student" oninput="filterStudents()" placeholder="ابحث بالاسم أو البريد أو الهاتف..." autocomplete="off">
                    <button type="button" id="clear_search" class="clear-search-btn" onclick="clearSearch()" style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button type="button" class="filter-tab active" data-filter="all" onclick="setFilter('all')">
                        الكل <span class="tab-count">({{ count($chats) }})</span>
                    </button>
                    <button type="button" class="filter-tab" data-filter="unread" onclick="setFilter('unread')">
                        غير مقروءة <span class="tab-count" id="tab_unread_count">({{ $totalUnread }})</span>
                    </button>
                </div>
            </div>

            <!-- Student Conversation Items -->
            <div class="conversations-list" id="student_list">
                @forelse($chats as $student)
                    @php
                        $studentName = $student->name_ar ?? $student->name ?? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'طالب';
                        $firstLetter = mb_substr($studentName, 0, 1);
                        $stageName = $student->stage ? ($student->stage->name_ar ?? $student->stage->name ?? 'توجيهي') : 'توجيهي';
                        $phone = $student->phone ?? $student->whatsapp ?? '';
                        $hasUnread = ($student->unread_count ?? 0) > 0;
                        $lastText = $student->last_message ?? 'لا توجد رسائل سابقة';
                        $lastTime = $student->last_message_time ?? '';
                        $isMe = ($student->last_sender_type === 'admin');
                    @endphp
                    <div onclick="loadChat({{ $student->id }}, '{{ addslashes($studentName) }}', '{{ addslashes($stageName) }}', '{{ addslashes($phone) }}', '{{ $student->status }}')"
                         class="conv-card {{ $hasUnread ? 'has-unread' : '' }} {{ (isset($selectedStudentId) && $selectedStudentId == $student->id) ? 'active' : '' }}"
                         id="user_{{ $student->id }}"
                         data-student-id="{{ $student->id }}"
                         data-unread="{{ $hasUnread ? '1' : '0' }}"
                         data-search="{{ mb_strtolower($studentName . ' ' . ($student->email ?? '') . ' ' . $phone . ' ' . $stageName) }}">
                        
                        <div class="conv-avatar-wrapper">
                            @if(!empty($student->photo))
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $studentName }}" class="conv-avatar-img">
                            @else
                                <div class="conv-avatar-initials">{{ $firstLetter }}</div>
                            @endif
                            <span class="conv-status-dot {{ $student->status === 'active' ? 'online' : 'inactive' }}" title="{{ $student->status === 'active' ? 'نشط' : 'حساب موقف' }}"></span>
                        </div>

                        <div class="conv-body">
                            <div class="conv-top-row">
                                <h4 class="conv-name" title="{{ $studentName }}">{{ $studentName }}</h4>
                                <span class="conv-time" id="time_{{ $student->id }}">{{ $lastTime }}</span>
                            </div>
                            <div class="conv-middle-row">
                                <span class="conv-stage-badge">{{ $stageName }}</span>
                            </div>
                            <div class="conv-bottom-row">
                                <p class="conv-snippet" id="snippet_{{ $student->id }}">
                                    @if($student->last_message)
                                        @if($isMe)<span class="snippet-prefix">أنت: </span>@endif{{ Str::limit($lastText, 35) }}
                                    @else
                                        <span class="no-msg-text">محادثة جديدة</span>
                                    @endif
                                </p>
                                <span class="unread-pill" id="badge_{{ $student->id }}" style="{{ $hasUnread ? '' : 'display: none;' }}">
                                    {{ $student->unread_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-list-placeholder">
                        <i class="fa-solid fa-user-group"></i>
                        <p>لا يوجد طلاب مسجلين حالياً</p>
                    </div>
                @endforelse

                <div id="no_results" class="empty-list-placeholder" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>لم يتم العثور على أي محادثة مطابقة للبحث</p>
                </div>
            </div>
        </aside>

        <!-- ==================== MAIN CHAT VIEWPORT ==================== -->
        <main class="inbox-chat-pane" id="chat_view">
            
            <!-- Empty State (when no chat is selected) -->
            <div id="chat_empty_state" class="chat-placeholder" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: none;' : 'display: flex;' }}">
                <div class="placeholder-graphic">
                    <div class="graphic-circle">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                </div>
                <h3>مركز المراسلات المباشرة</h3>
                <p>اختر طالباً من القائمة الجانبية لبدء المحادثة ومتابعة استفساراته وتقديم الدعم الأكاديمي والمالي.</p>
                <div class="quick-hints">
                    <div class="hint-item">
                        <i class="fa-solid fa-bolt"></i>
                        <span>يمكنك إرسال ردود سريعة بنقرة واحدة</span>
                    </div>
                    <div class="hint-item">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>إمكانية التحويل المباشر لمحادثة واتساب الرسمية</span>
                    </div>
                    <div class="hint-item">
                        <i class="fa-solid fa-bell"></i>
                        <span>وصول إشعارات فورية للطلاب على لوحة التحكم</span>
                    </div>
                </div>
            </div>

            <!-- Active Chat Header -->
            <header id="chat_header" class="active-header" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="header-student-info">
                    <button type="button" class="mobile-back-btn" onclick="toggleMobileView('sidebar')" title="العودة للقائمة">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    
                    <div class="active-avatar" id="active_avatar">
                        @if(isset($selectedStudent))
                            {{ mb_substr($selectedStudent->name_ar ?? $selectedStudent->name ?? 'ط', 0, 1) }}
                        @else
                            ط
                        @endif
                    </div>
                    
                    <div class="active-meta">
                        <div class="name-and-badges">
                            <h3 id="active_user_name" class="active-name">
                                {{ isset($selectedStudent) ? ($selectedStudent->name_ar ?? $selectedStudent->name ?? 'طالب') : 'اختر طالباً' }}
                            </h3>
                            <span id="active_stage_tag" class="header-stage-badge">
                                {{ isset($selectedStudent) && $selectedStudent->stage ? ($selectedStudent->stage->name_ar ?? $selectedStudent->stage->name ?? 'توجيهي') : 'توجيهي' }}
                            </span>
                        </div>
                        <div class="active-sub-meta">
                            <span class="active-status-text">
                                <span class="status-pulse-dot"></span> متصل في المنصة
                            </span>
                            <span class="sub-sep">•</span>
                            <span id="active_phone_display" class="active-phone">
                                <i class="fa-solid fa-phone"></i>
                                <span id="active_phone_number">{{ isset($selectedStudent) ? ($selectedStudent->phone ?? $selectedStudent->whatsapp ?? 'غير محدد') : '---' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="header-actions">
                    <!-- WhatsApp Link Button -->
                    <a href="#" id="btn_whatsapp_direct" target="_blank" class="action-btn btn-whatsapp" style="display: none;" title="محادثة عبر واتساب">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span class="btn-label">واتساب</span>
                    </a>
                    
                    <!-- View Profile Link -->
                    <a href="#" id="btn_view_profile" target="_blank" class="action-btn btn-profile" style="display: none;" title="عرض الملف الأكاديمي">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span class="btn-label">الملف الأكاديمي</span>
                    </a>

                    <!-- Refresh Messages Button -->
                    <button type="button" class="action-btn btn-refresh" onclick="fetchMessages(true)" title="تحديث الرسائل">
                        <i class="fa-solid fa-rotate-right" id="refresh_icon"></i>
                    </button>
                </div>
            </header>

            <!-- Messages Stream Area -->
            <div id="chat_messages" class="messages-area" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <div class="loading-messages">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span>جاري تحميل المحادثة...</span>
                </div>
            </div>

            <!-- Quick Reply Prompts Toolbar -->
            <div id="quick_replies_bar" class="canned-responses-bar" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: flex;' : 'display: none;' }}">
                <span class="canned-title">
                    <i class="fa-solid fa-bolt"></i> ردود سريعة:
                </span>
                <div class="canned-chips-container">
                    <button type="button" class="chip-btn" onclick="insertCanned('وعليكم السلام ورحمة الله، أهلاً بك! كيف يمكننا مساعدتك؟')">
                        👋 ترحيب
                    </button>
                    <button type="button" class="chip-btn" onclick="insertCanned('تم التحقق وتفعيل حسابك واشتراكك بنجاح، بالتوفيق!')">
                        ✅ تم تفعيل الاشتراك
                    </button>
                    <button type="button" class="chip-btn" onclick="insertCanned('يرجى تزويدنا بصورة إيصال السداد أو رقم الحوالة المالية لمطابقتها.')">
                        📄 طلب إيصال سداد
                    </button>
                    <button type="button" class="chip-btn" onclick="insertCanned('تم تحويل استفسارك إلى مدرس المادة، وسيقوم بالإجابة عليك قريباً.')">
                        👨‍🏫 إحالة للمعلم
                    </button>
                    <button type="button" class="chip-btn" onclick="insertCanned('تم حل المشكلة وتحديث البيانات في حسابك.')">
                        ✨ تم حل المشكلة
                    </button>
                </div>
            </div>

            <!-- Message Input Area -->
            <div id="input_area" class="input-container" style="{{ isset($selectedStudentId) && $selectedStudentId ? 'display: block;' : 'display: none;' }}">
                <form id="chatForm" onsubmit="event.preventDefault(); sendReply();" class="message-form">
                    <div class="input-row">
                        <textarea id="msg_input" 
                                  rows="1" 
                                  placeholder="اكتب ردك الأكاديمي هنا... (اضغط Enter للإرسال، Shift+Enter لسطر جديد)" 
                                  autocomplete="off" 
                                  required></textarea>
                        
                        <button type="submit" class="btn-send-message" id="btn_send" title="إرسال">
                            <span class="send-text">إرسال</span>
                            <i class="fa-solid fa-paper-plane send-icon"></i>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<style>
    /* =========================
       INBOX LUXURY STYLING
       ========================= */
    :root {
        --chat-navy: #0f172a;
        --chat-navy-light: #1e293b;
        --chat-primary: #1e3a8a;
        --chat-primary-hover: #1d4ed8;
        --chat-accent: #2563eb;
        --chat-surface: #ffffff;
        --chat-bg-soft: #f8fafc;
        --chat-border: #e2e8f0;
        --chat-border-subtle: #f1f5f9;
        --chat-text-dark: #0f172a;
        --chat-text-muted: #64748b;
        --chat-bubble-admin: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        --chat-bubble-student: #ffffff;
        --chat-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        --chat-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        --chat-shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        --radius-lg: 18px;
        --radius-md: 12px;
        --radius-sm: 8px;
    }

    .inbox-page-container {
        padding: 16px 20px;
        max-width: 1600px;
        margin: 0 auto;
        box-sizing: border-box;
    }

    .inbox-card {
        display: grid;
        grid-template-columns: 380px 1fr;
        height: calc(100vh - 130px);
        min-height: 550px;
        background: var(--chat-surface);
        border: 1px solid var(--chat-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--chat-shadow-lg);
    }

    /* ------------------------------
       SIDEBAR
       ------------------------------ */
    .inbox-sidebar {
        display: flex;
        flex-direction: column;
        background: var(--chat-surface);
        border-left: 1px solid var(--chat-border);
        min-width: 0;
        height: 100%;
    }

    .sidebar-head {
        padding: 20px 20px 14px 20px;
        background: var(--chat-surface);
        border-bottom: 1px solid var(--chat-border);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .head-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .title-with-icon {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .head-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
        color: var(--chat-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .head-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--chat-text-dark);
        margin: 0;
        line-height: 1.3;
    }

    .head-subtitle {
        font-size: 0.76rem;
        color: var(--chat-text-muted);
        margin: 0;
    }

    .badge-unread-total {
        background: #ef4444;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
        animation: pulseSoft 2s infinite;
    }

    @keyframes pulseSoft {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        right: 14px;
        color: var(--chat-text-muted);
        font-size: 0.88rem;
        pointer-events: none;
    }

    .search-wrapper input {
        width: 100%;
        padding: 10px 38px 10px 36px;
        background: var(--chat-bg-soft);
        border: 1px solid var(--chat-border);
        border-radius: var(--radius-md);
        font-size: 0.85rem;
        color: var(--chat-text-dark);
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .search-wrapper input:focus {
        background: #ffffff;
        border-color: var(--chat-accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    .clear-search-btn {
        position: absolute;
        left: 10px;
        background: none;
        border: none;
        color: var(--chat-text-muted);
        cursor: pointer;
        padding: 4px;
        font-size: 0.8rem;
    }

    .clear-search-btn:hover {
        color: var(--chat-text-dark);
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
    }

    .filter-tab {
        flex: 1;
        background: var(--chat-bg-soft);
        border: 1px solid var(--chat-border);
        color: var(--chat-text-muted);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }

    .filter-tab:hover {
        background: #e2e8f0;
        color: var(--chat-text-dark);
    }

    .filter-tab.active {
        background: var(--chat-primary);
        color: #ffffff;
        border-color: var(--chat-primary);
    }

    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 12px;
    }

    /* Individual Student Card in Sidebar */
    .conv-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 6px;
        border: 1px solid transparent;
        background: #ffffff;
        position: relative;
    }

    .conv-card:hover {
        background: var(--chat-bg-soft);
        border-color: var(--chat-border);
    }

    .conv-card.active {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .conv-card.active::before {
        content: '';
        position: absolute;
        right: 0;
        top: 10px;
        bottom: 10px;
        width: 4px;
        background: var(--chat-accent);
        border-radius: 4px 0 0 4px;
    }

    .conv-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .conv-avatar-initials {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.15rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }

    .conv-avatar-img {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--chat-border);
    }

    .conv-status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    .conv-status-dot.online {
        background: #10b981;
    }

    .conv-status-dot.inactive {
        background: #94a3b8;
    }

    .conv-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .conv-top-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 8px;
    }

    .conv-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--chat-text-dark);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-time {
        font-size: 0.7rem;
        color: var(--chat-text-muted);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .conv-middle-row {
        display: flex;
        align-items: center;
    }

    .conv-stage-badge {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        display: inline-block;
    }

    .conv-card.active .conv-stage-badge {
        background: #dbeafe;
        color: #1e40af;
    }

    .conv-bottom-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
    }

    .conv-snippet {
        font-size: 0.8rem;
        color: var(--chat-text-muted);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }

    .snippet-prefix {
        font-weight: 600;
        color: #334155;
    }

    .no-msg-text {
        font-style: italic;
        opacity: 0.6;
    }

    .unread-pill {
        background: #ef4444;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 12px;
        min-width: 18px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
    }

    .empty-list-placeholder {
        padding: 48px 20px;
        text-align: center;
        color: var(--chat-text-muted);
    }

    .empty-list-placeholder i {
        font-size: 2.2rem;
        color: #cbd5e1;
        margin-bottom: 12px;
        display: block;
    }

    .empty-list-placeholder p {
        font-size: 0.88rem;
        margin: 0;
    }

    /* ------------------------------
       MAIN CHAT PANE
       ------------------------------ */
    .inbox-chat-pane {
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        position: relative;
        height: 100%;
        min-width: 0;
    }

    /* Placeholder when no conversation chosen */
    .chat-placeholder {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
    }

    .placeholder-graphic .graphic-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
        margin: 0 auto 20px;
        box-shadow: 0 10px 20px -5px rgba(30, 58, 138, 0.3);
    }

    .chat-placeholder h3 {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--chat-text-dark);
        margin: 0 0 8px 0;
    }

    .chat-placeholder p {
        font-size: 0.92rem;
        color: var(--chat-text-muted);
        max-width: 440px;
        line-height: 1.6;
        margin: 0 0 24px 0;
    }

    .quick-hints {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 380px;
        text-align: right;
    }

    .hint-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid var(--chat-border);
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        color: #334155;
    }

    .hint-item i {
        color: var(--chat-accent);
        font-size: 1rem;
    }

    /* Active Header */
    .active-header {
        padding: 14px 24px;
        background: #ffffff;
        border-bottom: 1px solid var(--chat-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        z-index: 5;
    }

    .header-student-info {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .mobile-back-btn {
        display: none;
        background: none;
        border: none;
        color: var(--chat-text-dark);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 6px;
    }

    .active-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: var(--chat-shadow-sm);
    }

    .active-meta {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }

    .name-and-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .active-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--chat-text-dark);
        margin: 0;
    }

    .header-stage-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .active-sub-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: var(--chat-text-muted);
    }

    .status-pulse-dot {
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
    }

    .sub-sep {
        color: #cbd5e1;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid var(--chat-border);
        background: #ffffff;
        color: var(--chat-text-dark);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--chat-bg-soft);
        border-color: #cbd5e1;
    }

    .btn-whatsapp {
        background: #25d366;
        color: #ffffff;
        border-color: #22c55e;
    }

    .btn-whatsapp:hover {
        background: #16a34a;
        color: #ffffff;
    }

    .btn-profile {
        background: #f8fafc;
        color: var(--chat-primary);
        border-color: #cbd5e1;
    }

    .btn-profile:hover {
        background: #eff6ff;
        border-color: #93c5fd;
    }

    .btn-refresh {
        padding: 8px 10px;
    }

    /* Messages Display */
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        background-color: #f1f5f9;
        background-image: radial-gradient(#e2e8f0 1.2px, transparent 1.2px);
        background-size: 20px 20px;
    }

    .loading-messages {
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: var(--chat-text-muted);
        font-size: 0.9rem;
    }

    .empty-chat-message {
        margin: auto;
        text-align: center;
        color: var(--chat-text-muted);
        padding: 30px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        border-radius: var(--radius-md);
        border: 1px dashed var(--chat-border);
        max-width: 400px;
    }

    .empty-chat-message i {
        font-size: 2rem;
        color: var(--chat-accent);
        margin-bottom: 10px;
        display: block;
    }

    /* Message Rows and Bubbles */
    .message-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        width: 100%;
    }

    /* In RTL:
       Student (incoming) = on the Right (flex-start)
       Admin (outgoing) = on the Left (flex-end)
    */
    .message-row.is-student {
        justify-content: flex-start;
        flex-direction: row;
    }

    .message-row.is-admin {
        justify-content: flex-end;
        flex-direction: row-reverse;
    }

    .msg-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .message-row.is-student .msg-avatar-small {
        background: #e2e8f0;
        color: #475569;
    }

    .message-row.is-admin .msg-avatar-small {
        background: var(--chat-primary);
        color: #ffffff;
    }

    .message-bubble {
        max-width: 68%;
        min-width: 140px;
        padding: 12px 16px;
        position: relative;
        font-size: 0.92rem;
        line-height: 1.6;
        word-break: break-word;
        box-shadow: var(--chat-shadow-sm);
        animation: messageFadeIn 0.25s ease-out;
    }

    @keyframes messageFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-row.is-admin .message-bubble {
        background: var(--chat-bubble-admin);
        color: #ffffff;
        border-radius: 16px 16px 4px 16px;
    }

    .message-row.is-student .message-bubble {
        background: var(--chat-bubble-student);
        color: var(--chat-text-dark);
        border: 1px solid var(--chat-border);
        border-radius: 16px 16px 16px 4px;
    }

    .bubble-author {
        font-size: 0.74rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .message-row.is-admin .bubble-author {
        color: #93c5fd;
    }

    .message-row.is-student .bubble-author {
        color: var(--chat-primary);
    }

    .bubble-text {
        white-space: pre-wrap;
    }

    .bubble-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        margin-top: 6px;
        font-size: 0.68rem;
    }

    .message-row.is-admin .bubble-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    .message-row.is-student .bubble-meta {
        color: var(--chat-text-muted);
    }

    .bubble-meta i {
        font-size: 0.72rem;
    }

    /* Quick Reply Toolbar */
    .canned-responses-bar {
        background: #ffffff;
        border-top: 1px solid var(--chat-border);
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .canned-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--chat-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .canned-title i {
        color: #d97706;
    }

    .canned-chips-container {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .chip-btn {
        background: var(--chat-bg-soft);
        border: 1px solid var(--chat-border);
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 0.78rem;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        font-family: inherit;
        font-weight: 500;
    }

    .chip-btn:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: var(--chat-primary);
        transform: translateY(-1px);
    }

    /* Input Area */
    .input-container {
        padding: 14px 20px;
        background: #ffffff;
        border-top: 1px solid var(--chat-border);
    }

    .message-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-row {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--chat-bg-soft);
        border: 1px solid var(--chat-border);
        border-radius: var(--radius-md);
        padding: 6px 10px 6px 14px;
        transition: all 0.2s;
    }

    .input-row:focus-within {
        border-color: var(--chat-accent);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .input-row textarea {
        flex: 1;
        border: none;
        background: transparent;
        resize: none;
        outline: none;
        font-family: inherit;
        font-size: 0.92rem;
        line-height: 1.5;
        max-height: 120px;
        padding: 6px 0;
        color: var(--chat-text-dark);
    }

    .btn-send-message {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    .btn-send-message:hover {
        background: linear-gradient(135deg, #172554 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
    }

    .btn-send-message:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Scrollbar */
    .conversations-list::-webkit-scrollbar,
    .messages-area::-webkit-scrollbar,
    .canned-chips-container::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    .conversations-list::-webkit-scrollbar-thumb,
    .messages-area::-webkit-scrollbar-thumb,
    .canned-chips-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .inbox-page-container {
            padding: 8px;
        }
        .inbox-card {
            grid-template-columns: 1fr;
            height: calc(100vh - 100px);
            border-radius: 12px;
        }
        .inbox-chat-pane {
            display: none;
        }
        .inbox-card.mobile-active .inbox-sidebar {
            display: none;
        }
        .inbox-card.mobile-active .inbox-chat-pane {
            display: flex;
        }
        .mobile-back-btn {
            display: inline-block;
        }
        .message-bubble {
            max-width: 85%;
        }
        .btn-send-message .send-text {
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
        document.querySelectorAll('.filter-tab').forEach(b => {
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

        const cards = document.querySelectorAll('.conv-card');
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

        // Update browser URL query string without reloading page
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

        // Highlight active card in sidebar
        document.querySelectorAll('.conv-card').forEach(c => c.classList.remove('active'));
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
            <div class="loading-messages">
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                <span>جاري تحميل المحادثة...</span>
            </div>
        `;

        // Fetch messages immediately
        fetchMessages(true);

        // Auto-refresh poll every 3.5 seconds
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => fetchMessages(false), 3500);

        // Focus input
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

                // If student object returned, update header
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
                        <div class="empty-chat-message">
                            <i class="fa-regular fa-comment-dots"></i>
                            <h4 style="font-weight:700; color: #1e293b; margin-bottom: 6px;">لا توجد رسائل سابقة</h4>
                            <p style="font-size:0.85rem; margin:0;">ابدأ المحادثة مع الطالب عبر كتابة رسالة بالأسفل.</p>
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
        const emptyMsg = box.querySelector('.empty-chat-message');
        if (emptyMsg) emptyMsg.remove();

        const sender = (msg.sender_type || '').toLowerCase().trim();
        const isAdmin = (sender === 'admin');
        const time = msg.created_at_formatted || '';
        const studentName = document.getElementById('active_user_name').innerText;
        const authorLabel = isAdmin ? 'إدارة المنصة' : studentName;
        const avatarLetter = isAdmin ? '<i class="fa-solid fa-user-shield"></i>' : studentName.charAt(0);

        const row = document.createElement('div');
        row.className = `message-row ${isAdmin ? 'is-admin' : 'is-student'}`;
        row.innerHTML = `
            <div class="msg-avatar-small" title="${escapeHtml(authorLabel)}">
                ${avatarLetter}
            </div>
            <div class="message-bubble">
                <span class="bubble-author">${escapeHtml(authorLabel)}</span>
                <div class="bubble-text">${escapeHtml(msg.message)}</div>
                <div class="bubble-meta">
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
                snippet.innerHTML = `<span class="snippet-prefix">أنت: </span>` + escapeHtml(message.substring(0, 35));
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

    // Handle Enter key for sending (Shift+Enter for newline)
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
                targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // If card not found in DOM, fetch directly
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
    });
</script>
@endsection
