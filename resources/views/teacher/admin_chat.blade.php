@extends('layouts.app')

@section('title', __('مراسلة الإدارة العامة والشؤون الأكاديمية') . ' - ' . config('app.name', 'منارة التوجيهي'))

@section('content')
<div class="academic-chat-container">
    <div class="academic-chat-card">

        <!-- الترويسة الأكاديمية الملكية للمحادثة -->
        <header class="academic-chat-header">
            <div class="header-main-group">
                <div class="header-avatar-emblem">
                    <i class="fa-solid fa-building-columns"></i>
                    <span class="online-indicator-dot" title="{{ __('متصل ومتاح للرد') }}"></span>
                </div>
                <div class="header-text-group">
                    <div class="header-title-line">
                        <h2 class="chat-target-title">{{ __('الإدارة العامة والشؤون الأكاديمية') }}</h2>
                        <span class="palestine-inst-tag"><i class="fa-solid fa-award"></i> دولة فلسطين 🇵🇸</span>
                    </div>
                    <div class="header-sub-line">
                        <span class="status-live-badge"><span class="pulse-emerald"></span> {{ __('قناة المراسلات الرسمية المباشرة') }}</span>
                        <span class="sub-sep">•</span>
                        <span class="office-hours">{{ __('تواصل مباشر مع المشرف العام') }}</span>
                    </div>
                </div>
            </div>

            <div class="header-action-tools">
                <button type="button" class="tool-btn" id="soundToggleBtn" onclick="toggleAudioChime()" title="{{ __('كتم/تفعيل صوت الإشعارات') }}">
                    <i class="fa-solid fa-volume-high" id="soundIcon"></i>
                </button>
                <button type="button" class="tool-btn" onclick="fetchMessages(true)" title="{{ __('تحديث الرسائل') }}">
                    <i class="fa-solid fa-rotate-right" id="refreshIcon"></i>
                </button>
                <button type="button" class="tool-btn" onclick="scrollToBottomSmooth()" title="{{ __('الانتقال لآخر رسالة') }}">
                    <i class="fa-solid fa-angles-down"></i>
                </button>
            </div>
        </header>

        <!-- كبسولات المراسلات السريعة المعتمدة للمعلم -->
        <div class="canned-prompts-strip">
            <span class="canned-label"><i class="fa-solid fa-bolt text-amber"></i> {{ __('موضوعات سريعة:') }}</span>
            <div class="canned-scroll-lane">
                <button type="button" class="canned-pill" onclick="insertPrompt('السلام عليكم إدارة المنصة، أرجو اعتماد الاختبار الجديد ومراجعته تمهيداً لنشره للطلاب 📝')">
                    📝 اعتماد اختبار جديد
                </button>
                <button type="button" class="canned-pill" onclick="insertPrompt('السلام عليكم، قمت برفع شروحات وملازم جديدة للمادة وأرجو مراجعة التنسيق 📄')">
                    📄 إضافة شروحات وملازم
                </button>
                <button type="button" class="canned-pill" onclick="insertPrompt('السلام عليكم، أرجو الاطلاع على كشف المستحقات المالية ونسب التسجيل للشهر الحالي 💰')">
                    💰 استفسار مالي ومستحقات
                </button>
                <button type="button" class="canned-pill" onclick="insertPrompt('لدي استفسار تقني بخصوص رفع الفيديوهات التعليمية على السيرفر ⚙️')">
                    ⚙️ استفسار تقني للمنصة
                </button>
            </div>
        </div>

        <!-- مسار تدفق الرسائل الأكاديمي -->
        <div class="academic-messages-feed" id="chat_messages">
            <div class="empty-feed-placeholder" id="emptyPlaceholder">
                <div class="ph-icon-box">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3>{{ __('قناة التواصل المباشر مع إدارة المنصة') }}</h3>
                <p>{{ __('مرحباً بك أستاذنا الفاضل. يمكنك طرح أي استفسار أو مراسلة المشرف العام للشؤون الأكاديمية وسيقوم بالرد والمتابعة معك فوراً.') }}</p>
            </div>
        </div>

        <!-- شريط الإدخال المتطور الكلاسيكي -->
        <div class="academic-composer-pane">
            <form id="teacherAdminChatForm" onsubmit="event.preventDefault(); sendTeacherReply();" class="composer-form-inner">
                <div class="composer-input-wrapper">
                    <textarea 
                        id="msg_input" 
                        class="academic-textarea" 
                        placeholder="{{ __('اكتب رسالتك أو استفسارك لإدارة المنصة هنا... (اضغط Enter للإرسال)') }}" 
                        rows="1" 
                        autocomplete="off"
                        onkeydown="handleComposerKeydown(event)"
                        oninput="autoExpandTextarea(this)"></textarea>

                    <button type="submit" id="btn_send" class="academic-send-btn" title="{{ __('إرسال الرسالة') }}">
                        <span>{{ __('إرسال') }}</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="composer-footer-hint">
                    <span><i class="fa-regular fa-keyboard"></i> {{ __('اضغط Enter للإرسال المباشر، أو Shift + Enter لسطر جديد') }}</span>
                    <span id="charCount" class="font-mono text-muted">0 / 1000</span>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
/* =========================================================
   CLASSIC ACADEMIC CHAT STYLING (Institutional Prestige)
   ========================================================= */
.academic-chat-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 0 20px;
}

.academic-chat-card {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 130px);
    min-height: 520px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

/* Header */
.academic-chat-header {
    padding: 14px 22px;
    background: #ffffff;
    border-bottom: 1.5px solid #e2e8f0;
    border-top: 3.5px solid #1e3a8a;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.header-main-group {
    display: flex;
    align-items: center;
    gap: 14px;
}

.header-avatar-emblem {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #1e3a8a;
    color: #ffffff;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    position: relative;
    flex-shrink: 0;
}

.online-indicator-dot {
    position: absolute;
    bottom: -2px;
    left: -2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #10b981;
    border: 2px solid #ffffff;
}

.header-title-line {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 2px;
}

.chat-target-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.palestine-inst-tag {
    font-size: 0.72rem;
    font-weight: 800;
    color: #1e3a8a;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 2px 8px;
    border-radius: 4px;
}

.header-sub-line {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    color: #64748b;
}

.status-live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #059669;
    font-weight: 700;
}

.pulse-emerald {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    display: inline-block;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.sub-sep { color: #cbd5e1; }

.header-action-tools {
    display: flex;
    align-items: center;
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
    transition: all 0.15s ease;
}

.tool-btn:hover {
    background: #eff6ff;
    color: #1e3a8a;
    border-color: #93c5fd;
}

/* Canned Prompts */
.canned-prompts-strip {
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
    gap: 6px;
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
    padding: 4px 12px;
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

/* Feed */
.academic-messages-feed {
    flex: 1;
    padding: 22px;
    background: #f8fafc;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.empty-feed-placeholder {
    margin: auto;
    text-align: center;
    max-width: 480px;
    padding: 30px 20px;
    color: #64748b;
}

.ph-icon-box {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: #e0f2fe;
    color: #0284c7;
    display: grid;
    place-items: center;
    font-size: 1.8rem;
    margin: 0 auto 14px;
}

.empty-feed-placeholder h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}

.empty-feed-placeholder p {
    font-size: 0.85rem;
    line-height: 1.6;
    margin: 0;
}

/* Bubbles */
.academic-msg-bubble {
    max-width: 78%;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.92rem;
    line-height: 1.6;
    position: relative;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.msg-from-teacher {
    align-self: flex-start;
    background: #1e3a8a;
    color: #ffffff;
    border: 1px solid #1e3a8a;
    border-top-right-radius: 2px;
}

.msg-from-admin {
    align-self: flex-end;
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    border-top-left-radius: 2px;
}

.msg-meta-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 4px;
    font-size: 0.7rem;
}

.msg-from-teacher .msg-meta-row {
    color: #bfdbfe;
}

.msg-from-admin .msg-meta-row {
    color: #64748b;
}

/* Composer */
.academic-composer-pane {
    padding: 14px 20px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.composer-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.academic-textarea {
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
    transition: border-color 0.15s, background-color 0.15s;
}

.academic-textarea:focus {
    border-color: #1e3a8a;
    background: #ffffff;
}

.academic-send-btn {
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

.academic-send-btn:hover {
    background: #0f172a;
    transform: translateY(-1px);
}

.composer-footer-hint {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
    font-size: 0.73rem;
    color: #64748b;
}

/* Responsive */
@media (max-width: 768px) {
    .academic-chat-card {
        height: calc(100vh - 80px);
        border-radius: 0;
        border: none;
    }
    .academic-msg-bubble {
        max-width: 90%;
    }
}
</style>

<script>
    const feed = document.getElementById('chat_messages');
    const msgInput = document.getElementById('msg_input');
    const charCountEl = document.getElementById('charCount');
    let lastContentHash = "";
    let soundEnabled = true;

    // Audio chime effect
    const audioChime = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbqWE2M1yct9e6ei8xWpu50rJ3LS5ZoLTIsHMwLlimtMWmcTItV5+wvKZwLCxVoK6yqG0vLFKcq62nbjAsUKKkpqJuLS1No6Ghnm8tLUueoJ+cbistTZubm5prKy1MmpeVlmcrLEuXlZSUZiwtSpOUk5JkLC1KkpKSkmMsLEqSkZCRZCws');

    function toggleAudioChime() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        if (icon) {
            icon.className = soundEnabled ? 'fa-solid fa-volume-high' : 'fa-solid fa-volume-xmark';
        }
    }

    function playMessageNotification() {
        if (!soundEnabled) return;
        try {
            audioChime.currentTime = 0;
            audioChime.play().catch(() => {});
        } catch(e) {}
    }

    function insertPrompt(text) {
        msgInput.value = text;
        autoExpandTextarea(msgInput);
        msgInput.focus();
    }

    function autoExpandTextarea(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        if (charCountEl) {
            charCountEl.innerText = `${el.value.length} / 1000`;
        }
    }

    function handleComposerKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendTeacherReply();
        }
    }

    function scrollToBottomSmooth() {
        if (feed) {
            feed.scrollTo({ top: feed.scrollHeight, behavior: 'smooth' });
        }
    }

    function fetchMessages(forceScroll = false) {
        const refreshIcon = document.getElementById('refreshIcon');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        axios.get("{{ route('teacher.admin.chat.messages') }}")
            .then(res => {
                const messages = res.data.messages || [];
                const placeholder = document.getElementById('emptyPlaceholder');

                if (messages.length === 0) {
                    if (placeholder) placeholder.style.display = 'block';
                    return;
                }

                if (placeholder) placeholder.style.display = 'none';

                const newHash = JSON.stringify(messages);
                if (newHash === lastContentHash && !forceScroll) return;

                const isNearBottom = feed.scrollHeight - feed.scrollTop <= feed.clientHeight + 120;
                const isNewMessageFromOther = lastContentHash !== "" && messages.length > 0 && messages[messages.length - 1].sender_type !== 'teacher';

                lastContentHash = newHash;

                let html = '';
                messages.forEach(m => {
                    const isMe = (m.sender_type === 'teacher');
                    const time = m.created_at_formatted || 'الآن';
                    html += `
                        <div class="academic-msg-bubble ${isMe ? 'msg-from-teacher' : 'msg-from-admin'}">
                            <div style="white-space: pre-wrap;">${escapeHtml(m.message)}</div>
                            <div class="msg-meta-row font-mono">
                                <span>${isMe ? '{{ __("أنت (المعلم)") }}' : '{{ __("الإدارة العامة") }}'}</span>
                                <span>•</span>
                                <span>${time} ${isMe ? '✓✓' : ''}</span>
                            </div>
                        </div>
                    `;
                });

                feed.innerHTML = html;

                if (isNewMessageFromOther) {
                    playMessageNotification();
                }

                if (forceScroll || isNearBottom) {
                    feed.scrollTop = feed.scrollHeight;
                }
            })
            .catch(err => {
                console.error('Fetch Teacher-Admin messages error:', err);
            })
            .finally(() => {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            });
    }

    function sendTeacherReply() {
        const text = msgInput.value.trim();
        if (!text) return;

        const sendBtn = document.getElementById('btn_send');
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        msgInput.value = '';
        msgInput.style.height = 'auto';
        if (charCountEl) charCountEl.innerText = '0 / 1000';

        // Optimistic render
        const placeholder = document.getElementById('emptyPlaceholder');
        if (placeholder) placeholder.style.display = 'none';

        const optimisticBubble = document.createElement('div');
        optimisticBubble.className = 'academic-msg-bubble msg-from-teacher';
        optimisticBubble.innerHTML = `
            <div style="white-space: pre-wrap;">${escapeHtml(text)}</div>
            <div class="msg-meta-row font-mono">
                <span>{{ __("أنت (المعلم)") }}</span>
                <span>•</span>
                <span>الآن ✓</span>
            </div>
        `;
        feed.appendChild(optimisticBubble);
        feed.scrollTop = feed.scrollHeight;

        axios.post("{{ route('teacher.admin.chat.send') }}", {
            message: text,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            fetchMessages(true);
        })
        .catch(err => {
            alert("{{ __('تعذر إرسال الرسالة للإدارة، يرجى المحاولة مرة أخرى.') }}");
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
        fetchMessages(true);
        setInterval(() => fetchMessages(false), 3500);
    });
</script>
@endsection
