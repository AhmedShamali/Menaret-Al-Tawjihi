@extends('layouts.app')

@section('title', __('المركز الأكاديمي للذكاء الاصطناعي والمعلم الذكي') . ' - ' . config('app.name', 'Step by Step'))

@section('content')
<div class="ai-hub-container" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    <!-- شريط المسار والترويسة الكلاسيكية الفاخرة (Header & Breadcrumb) -->
    <div class="ai-hub-header-card">
        <div class="ai-hub-header-left">
            <div class="ai-hub-badge">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>{{ __('المركز الأكاديمي الشامل للذكاء الاصطناعي') }}</span>
                <span class="ai-hub-badge-version">Pro 2.0</span>
            </div>
            <h1 class="ai-hub-title">{{ __('المعلم والمساعد الأكاديمي الذكي') }}</h1>
            <p class="ai-hub-subtitle">
                {{ __('شروحات موسوعية، حل مسائل فيزيائية ورياضية خطوة بخطوة بالرموز العربية والإنجليزية، وإجابة عن أي استفسار عام وأكاديمي.') }}
            </p>
        </div>

        <div class="ai-hub-header-actions">
            <!-- تبديل الرموز -->
            <button type="button" id="hubSymbolToggleBtn" class="ai-hub-action-pill" title="{{ __('تبديل لغة الرموز الرياضية والعلمية') }}">
                <i class="fa-solid fa-square-root-variable" style="color: #1d4ed8;"></i>
                <span id="hubSymbolText">الرموز: عربي (ق = ك × ت)</span>
            </button>

            <!-- عارض الخريطة الذهنية -->
            <button type="button" id="hubMindmapToggleBtn" class="ai-hub-action-pill" title="{{ __('عرض الخريطة الذهنية التفاعلية للمفهوم') }}">
                <i class="fa-solid fa-diagram-project" style="color: #d97706;"></i>
                <span>{{ __('خريطة المفاهيم') }}</span>
            </button>

            <!-- طباعة وتصدير PDF -->
            <button type="button" id="hubExportPdfBtn" class="ai-hub-action-pill" title="{{ __('طباعة أو حفظ المحادثة بصيغة PDF') }}">
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i>
                <span>{{ __('تصدير PDF') }}</span>
            </button>
        </div>
    </div>

    <!-- الشبكة المركزية (السايدبار الجانبي لسجل المحادثات + مساحة المحادثة الرئيسية) -->
    <div class="ai-hub-grid">

        <!-- 1. الشريط الجانبي لسجل المحادثات (Conversations Sidebar) -->
        <aside class="ai-hub-sidebar">
            <div class="ai-sidebar-top">
                <button type="button" id="hubNewChatBtn" class="ai-new-chat-btn">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ __('محادثة جديدة') }}</span>
                </button>
            </div>

            <!-- بطاقة المستخدم الحالية -->
            <div class="ai-user-profile-badge">
                <div class="ai-user-avatar">
                    {{ mb_substr($actor['name'] ?? 'U', 0, 1, 'UTF-8') }}
                </div>
                <div class="ai-user-meta">
                    <span class="ai-user-name">{{ $actor['name'] ?? 'مستخدم المنصة' }}</span>
                    <span class="ai-user-role-label">
                        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                        {{ $actor['type'] === 'student' ? 'طالب توجيهي معتمد' : 'كادر تعليمي معتمد' }}
                    </span>
                </div>
            </div>

            <!-- قائمة المحادثات السابقة -->
            <div class="ai-conv-list-header">
                <span>{{ __('سجل المحادثات السابقة') }}</span>
                <span class="ai-conv-count" id="hubConvCountBadge">{{ count($conversations) }}</span>
            </div>

            <div class="ai-conv-list" id="hubConversationsList">
                @forelse($conversations as $conv)
                    <div class="ai-conv-item" data-id="{{ $conv->id }}">
                        <div class="ai-conv-item-main" onclick="loadConversation('{{ $conv->id }}')">
                            <i class="fa-regular fa-message ai-conv-icon"></i>
                            <div class="ai-conv-details">
                                <span class="ai-conv-title" title="{{ $conv->title }}">{{ $conv->title }}</span>
                                <span class="ai-conv-date">
                                    {{ $conv->created_at ? $conv->created_at->diffForHumans() : '' }} &bull; 
                                    <span class="ai-notation-tag">{{ $conv->symbol_mode === 'en' ? 'EN' : 'عربي' }}</span>
                                </span>
                            </div>
                        </div>
                        <button type="button" class="ai-conv-del-btn" title="{{ __('حذف هذه المحادثة') }}" onclick="deleteConversation('{{ $conv->id }}', event)">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                @empty
                    <div class="ai-empty-convs" id="hubEmptyConvsNotice">
                        <i class="fa-solid fa-comments"></i>
                        <p>{{ __('لا توجد محادثات سابقة، ابدأ استفسارك الأول الآن!') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- أسفل الشريط الجانبي: مسح كافة السجلات -->
            <div class="ai-sidebar-footer">
                <button type="button" id="hubClearAllBtn" class="ai-clear-all-btn">
                    <i class="fa-solid fa-trash-arrow-up"></i>
                    <span>{{ __('مسح كافة المحادثات') }}</span>
                </button>
            </div>
        </aside>

        <!-- 2. مساحة العمل والمحادثة الرئيسية (Main Chat Workspace) -->
        <main class="ai-hub-main">

            <!-- عارض الخريطة المفاهيمية الذكية (Smart Mindmap Drawer) -->
            <div id="hubMindmapDrawer" class="ai-mindmap-drawer" style="display: none;">
                <div class="ai-mindmap-header">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-diagram-project" style="color: #d97706;"></i>
                        <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ __('خريطة المفاهيم والروابط الأكاديمية الذكية') }}</h4>
                    </div>
                    <button type="button" class="ai-icon-btn" onclick="toggleMindmap()" title="{{ __('إغلاق') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="ai-mindmap-tree" id="hubMindmapTree">
                    <div class="tree-node tree-root">
                        <span>🎓 المبحث الأكاديمي المركزي</span>
                        <div class="tree-branches">
                            <div class="tree-branch">
                                <span class="tree-pill pill-blue">📐 المفاهيم والقوانين الرياضية</span>
                                <div class="tree-sub">
                                    <span class="tree-leaf">صيغة القانون (عربي / English)</span>
                                    <span class="tree-leaf">وحدات القياس بالنظام الدولي (SI)</span>
                                </div>
                            </div>
                            <div class="tree-branch">
                                <span class="tree-pill pill-amber">⚡ التطبيقات والمسائل الوزارية</span>
                                <div class="tree-sub">
                                    <span class="tree-leaf">استخراج المعطيات والمجاهيل</span>
                                    <span class="tree-leaf">التعويض وحساب الناتج خطوة بخطوة</span>
                                </div>
                            </div>
                            <div class="tree-branch">
                                <span class="tree-pill pill-emerald">💡 النصائح الذهبية وتثبيت الفهم</span>
                                <div class="tree-sub">
                                    <span class="tree-leaf">أبرز الأخطاء الشائعة لتفاديها</span>
                                    <span class="tree-leaf">سؤال مقترح لاختبار نفسك</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- منطقة الرسائل والمحادثة النشطة -->
            <div class="ai-hub-messages-box" id="hubMessagesBox">

                <!-- بطاقة الترحيب الموسعة الكلاسيكية -->
                <div class="ai-hub-welcome-banner" id="hubWelcomeBanner">
                    <div class="ai-welcome-icon-box">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div class="ai-welcome-info">
                        <h2>{{ __('أهلاً بك في فضاء الذكاء الاصطناعي لمنارة التوجيهي!') }}</h2>
                        <p>
                            {{ __('المعلم الذكي جاهز للإجابة بدقة بالغة عن أي مسألة أو سؤال في جميع مباحث المنهاج الوزاري الفلسطيني والثقافة العامة.') }}
                        </p>
                    </div>

                    <!-- مواضيع سريعة مقترحة للاختيار المباشر -->
                    <div class="ai-suggested-topics-grid">
                        <div class="ai-topic-card" onclick="sendQuickPrompt('اشرح لي قانون نيوتن الثاني في الفيزياء وحل مسألة تطبيقية عليه بالرموز')">
                            <div class="ai-topic-icon icon-blue"><i class="fa-solid fa-atom"></i></div>
                            <h4>{{ __('فيزياء التوجيهي') }}</h4>
                            <p>{{ __('قوانين الحركة، نيوتن الثاني، الشغل والطاقة بالرموز المعتمدة.') }}</p>
                        </div>

                        <div class="ai-topic-card" onclick="sendQuickPrompt('ما هي قواعد الاشتقاق الأساسية في الرياضيات مع أمثلة بالخطوات؟')">
                            <div class="ai-topic-icon icon-emerald"><i class="fa-solid fa-infinity"></i></div>
                            <h4>{{ __('الرياضيات والتفاضل') }}</h4>
                            <p>{{ __('قواعد الاشتقاق، التكامل غير المحدود، والنهايات خطوة بخطوة.') }}</p>
                        </div>

                        <div class="ai-topic-card" onclick="sendQuickPrompt('كيف أحسب معدلي التوجيهي وما هي الأوزان النسبية للمواد الأساسية والاختيارية؟')">
                            <div class="ai-topic-icon icon-amber"><i class="fa-solid fa-calculator"></i></div>
                            <h4>{{ __('حساب معدل التوجيهي') }}</h4>
                            <p>{{ __('طريقة احتساب المعدل الوزاري بدقة وتوزيع أوزان المقررات.') }}</p>
                        </div>

                        <div class="ai-topic-card" onclick="sendQuickPrompt('أعطني خطة عملية لتنظيم وقتي الدراسي ومراجعة مادتين يومياً')">
                            <div class="ai-topic-icon icon-purple"><i class="fa-solid fa-clock"></i></div>
                            <h4>{{ __('تنظيم جدول المذاكرة') }}</h4>
                            <p>{{ __('توزيع الساعات، تقنية بومودورو، والتغلب على التشتت.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- شريط جاري التفكير / الصياغة -->
                <div id="hubTypingIndicator" class="ai-hub-typing" style="display: none;">
                    <div class="ai-typing-pulse-dot"></div>
                    <span>{{ __('المعلم الذكي يقوم بالتحليل والصياغة الرياضية الدقيقة...') }}</span>
                </div>

            </div>

            <!-- شريط الصورة المرفقة إن وجدت -->
            <div id="hubImagePreviewBar" class="ai-hub-img-preview-bar" style="display: none;">
                <img id="hubAttachedImg" src="" alt="{{ __('الصورة المرفقة') }}">
                <div class="ai-hub-img-info">
                    <span class="ai-hub-img-title">{{ __('مسألة أو تمرين مرفق للتحليل الفوري') }}</span>
                    <span class="ai-hub-img-subtitle">{{ __('سيتم التعرف على المعادلات والرموز وحلها بالخطوات') }}</span>
                </div>
                <button type="button" class="ai-hub-remove-img" onclick="removeAttachedImage()" title="{{ __('إلغاء') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- شريط الإدخال المتطور في أسفل الصفحة -->
            <div class="ai-hub-input-box">
                <form id="hubChatForm" class="ai-hub-form" onsubmit="return false;">
                    
                    <input type="file" id="hubFileInput" accept="image/*" style="display: none;">

                    <!-- أزرار الإجراءات الإضافية -->
                    <div class="ai-hub-form-tools">
                        <!-- رفع صورة مسألة -->
                        <button type="button" class="ai-tool-btn" id="hubUploadImgBtn" title="{{ __('رفع صورة مسألة أو معادلة من الكتاب') }}">
                            <i class="fa-solid fa-camera"></i>
                            <span class="tool-btn-label">{{ __('صورة مسألة') }}</span>
                        </button>

                        <!-- إدخال صوتي -->
                        <button type="button" class="ai-tool-btn" id="hubVoiceBtn" title="{{ __('إملاء صوتي بالمايكروفون') }}">
                            <i class="fa-solid fa-microphone"></i>
                            <span class="tool-btn-label">{{ __('صوت') }}</span>
                        </button>

                        <!-- مؤشر لغة الرموز الحالية -->
                        <div class="ai-mode-pill">
                            <i class="fa-solid fa-circle-dot" style="color: #10b981; font-size: 0.65rem;"></i>
                            <span id="hubActiveModeBadge">{{ __('الرموز: عربي') }}</span>
                        </div>
                    </div>

                    <!-- حقل النص المرن -->
                    <div class="ai-hub-textarea-wrapper">
                        <textarea id="hubPromptInput" rows="2" class="ai-hub-textarea" placeholder="{{ __('اكتب سؤالك هنا بالتفصيل، مسألة فيزياء، معادلة رياضيات، إعراب، أو أي استفسار تريده...') }}" maxlength="5000"></textarea>
                        
                        <button type="submit" id="hubSubmitBtn" class="ai-hub-send-btn" title="{{ __('إرسال (Enter)') }}">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ __('إرسال') }}</span>
                        </button>
                    </div>

                    <div class="ai-hub-disclaimer">
                        <span>💡 {{ __('مفتاح Shift+Enter لإنشاء سطر جديد. يمكنك التحويل بين الرموز العربية والإنجليزية في أي وقت من الترويسة.') }}</span>
                    </div>
                </form>
            </div>

        </main>

    </div>

</div>

<!-- تضمين مكتبات KaTeX للرموز الرياضية و marked.js لتنسيق الماركداون -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
/* =========================================================================
   التصميم الكلاسيكي الفاخر للمركز الأكاديمي الشامل للذكاء الاصطناعي
   ========================================================================= */
.ai-hub-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 16px 20px 40px;
    font-family: inherit;
    --hub-primary: #1d4ed8;
    --hub-primary-hover: #1e40af;
    --hub-primary-soft: #eff6ff;
    --hub-gold: #d97706;
    --hub-gold-soft: #fffbeb;
    --hub-surface: #ffffff;
    --hub-bg: #f8fafc;
    --hub-border: #e2e8f0;
    --hub-border-light: #f1f5f9;
    --hub-text-main: #0f172a;
    --hub-text-muted: #64748b;
    --hub-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08), 0 4px 12px -4px rgba(15, 23, 42, 0.04);
}

/* ترويسة الصفحة */
.ai-hub-header-card {
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
    border: 1px solid var(--hub-border);
    border-radius: 18px;
    padding: 24px 28px;
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: var(--hub-shadow);
}
.ai-hub-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--hub-primary-soft);
    color: var(--hub-primary);
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 800;
    margin-bottom: 8px;
}
.ai-hub-badge-version {
    background: var(--hub-gold-soft);
    color: var(--hub-gold);
    border: 1px solid #fde68a;
    padding: 1px 7px;
    border-radius: 6px;
    font-size: 0.7rem;
}
.ai-hub-title {
    margin: 0 0 6px;
    font-size: 1.55rem;
    font-weight: 850;
    color: var(--hub-text-main);
    letter-spacing: -0.3px;
}
.ai-hub-subtitle {
    margin: 0;
    font-size: 0.88rem;
    color: var(--hub-text-muted);
    max-width: 680px;
    line-height: 1.6;
}
.ai-hub-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.ai-hub-action-pill {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 12px;
    padding: 9px 15px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--hub-text-main);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.03);
}
.ai-hub-action-pill:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

/* الشبكة الرئيسية */
.ai-hub-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 22px;
    align-items: start;
}

/* 1. السايدبار الجانبي */
.ai-hub-sidebar {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 18px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    box-shadow: var(--hub-shadow);
    max-height: calc(100vh - 200px);
    position: sticky;
    top: 90px;
}
.ai-new-chat-btn {
    width: 100%;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.92rem;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.25);
    transition: all 0.2s;
}
.ai-new-chat-btn:hover {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    transform: translateY(-1px);
}
.ai-user-profile-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid var(--hub-border-light);
    border-radius: 12px;
}
.ai-user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #ffffff;
    font-size: 1rem;
    font-weight: 800;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}
.ai-user-meta {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.ai-user-name {
    font-size: 0.84rem;
    font-weight: 800;
    color: var(--hub-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ai-user-role-label {
    font-size: 0.72rem;
    color: var(--hub-text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

/* قائمة المحادثات */
.ai-conv-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--hub-text-muted);
    padding: 0 4px;
    text-transform: uppercase;
}
.ai-conv-count {
    background: #e2e8f0;
    color: #334155;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 1px 7px;
    border-radius: 999px;
}
.ai-conv-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    overflow-y: auto;
    max-height: 420px;
    padding-inline-end: 4px;
}
.ai-conv-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid transparent;
    transition: all 0.15s;
}
.ai-conv-item:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.ai-conv-item.active-conv {
    background: var(--hub-primary-soft);
    border-color: #bfdbfe;
}
.ai-conv-item-main {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    min-width: 0;
}
.ai-conv-icon {
    color: #94a3b8;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.ai-conv-item.active-conv .ai-conv-icon {
    color: var(--hub-primary);
}
.ai-conv-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.ai-conv-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--hub-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ai-conv-date {
    font-size: 0.68rem;
    color: var(--hub-text-muted);
}
.ai-notation-tag {
    background: #f1f5f9;
    padding: 1px 5px;
    border-radius: 4px;
    font-size: 0.64rem;
    font-weight: 700;
}
.ai-conv-del-btn {
    background: transparent;
    border: none;
    color: #cbd5e1;
    font-size: 0.82rem;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    opacity: 0;
    transition: all 0.15s;
}
.ai-conv-item:hover .ai-conv-del-btn {
    opacity: 1;
}
.ai-conv-del-btn:hover {
    color: #ef4444;
    background: #fee2e2;
}
.ai-empty-convs {
    text-align: center;
    padding: 24px 10px;
    color: #94a3b8;
    font-size: 0.8rem;
}
.ai-empty-convs i {
    font-size: 1.8rem;
    margin-bottom: 6px;
    display: block;
    color: #cbd5e1;
}
.ai-sidebar-footer {
    border-top: 1px solid var(--hub-border-light);
    padding-top: 10px;
    margin-top: auto;
}
.ai-clear-all-btn {
    width: 100%;
    background: transparent;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 8px;
    font-size: 0.76rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.15s;
}
.ai-clear-all-btn:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #dc2626;
}

/* 2. مساحة المحادثة الرئيسية */
.ai-hub-main {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    min-height: 680px;
    box-shadow: var(--hub-shadow);
    overflow: hidden;
}

/* الخريطة الذهنية */
.ai-mindmap-drawer {
    background: #fafaf9;
    border-bottom: 1.5px solid #e7e5e4;
    padding: 16px 20px;
    animation: mindmapSlide 0.25s ease-out;
}
@keyframes mindmapSlide {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.ai-mindmap-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.ai-mindmap-tree {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
}
.tree-node.tree-root > span {
    font-weight: 800;
    font-size: 0.92rem;
    color: #1e3a8a;
    display: block;
    margin-bottom: 12px;
}
.tree-branches {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
}
.tree-branch {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
}
.tree-pill {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 800;
    margin-bottom: 8px;
}
.pill-blue { background: #dbeafe; color: #1e40af; }
.pill-amber { background: #fef3c7; color: #92400e; }
.pill-emerald { background: #d1fae5; color: #065f46; }
.tree-sub {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.tree-leaf {
    font-size: 0.74rem;
    color: #475569;
    padding-inline-start: 12px;
    position: relative;
}
.tree-leaf::before {
    content: "•";
    position: absolute;
    right: 2px;
    color: #94a3b8;
}

/* صندوق الرسائل */
.ai-hub-messages-box {
    flex: 1;
    padding: 24px;
    background: #f8fafc;
    overflow-y: auto;
    max-height: 580px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* بنر الترحيب والمواضيع المقترحة */
.ai-hub-welcome-banner {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
}
.ai-welcome-icon-box {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    font-size: 1.6rem;
    display: grid;
    place-items: center;
    margin: 0 auto 14px;
    box-shadow: 0 6px 16px rgba(29, 78, 216, 0.25);
}
.ai-welcome-info h2 {
    margin: 0 0 8px;
    font-size: 1.25rem;
    font-weight: 850;
    color: var(--hub-text-main);
}
.ai-welcome-info p {
    margin: 0 auto 20px;
    font-size: 0.86rem;
    color: var(--hub-text-muted);
    max-width: 600px;
    line-height: 1.6;
}
.ai-suggested-topics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    text-align: right;
}
.ai-topic-card {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 12px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.2s;
}
.ai-topic-card:hover {
    border-color: #93c5fd;
    background: var(--hub-primary-soft);
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.08);
}
.ai-topic-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    font-size: 0.95rem;
    margin-bottom: 10px;
}
.icon-blue { background: #eff6ff; color: #1d4ed8; }
.icon-emerald { background: #ecfdf5; color: #059669; }
.icon-amber { background: #fffbeb; color: #d97706; }
.icon-purple { background: #f5f3ff; color: #7c3aed; }
.ai-topic-card h4 {
    margin: 0 0 4px;
    font-size: 0.88rem;
    font-weight: 800;
    color: #0f172a;
}
.ai-topic-card p {
    margin: 0;
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.45;
}

/* تنسيق فقاعات الرسائل في Hub */
.ai-hub-msg {
    display: flex;
    gap: 12px;
    max-width: 88%;
    animation: hubMsgFade 0.22s ease-out;
}
@keyframes hubMsgFade {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.ai-hub-msg-user {
    align-self: flex-end;
    flex-direction: row-reverse;
}
.ai-hub-msg-assistant {
    align-self: flex-start;
}
.ai-hub-msg-avatar {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.ai-hub-msg-user .ai-hub-msg-avatar {
    background: #e2e8f0;
    color: #1e293b;
}
.ai-hub-msg-assistant .ai-hub-msg-avatar {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(29, 78, 216, 0.25);
}
.ai-hub-msg-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.ai-hub-msg-meta {
    font-size: 0.74rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ai-hub-msg-user .ai-hub-msg-meta {
    justify-content: flex-end;
}
.ai-hub-msg-bubble {
    background: #ffffff;
    border: 1px solid var(--hub-border);
    border-radius: 16px;
    padding: 14px 18px;
    font-size: 0.92rem;
    line-height: 1.7;
    color: #1e293b;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.ai-hub-msg-user .ai-hub-msg-bubble {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.22);
}
.ai-hub-msg-bubble code {
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    color: #0369a1;
    font-family: monospace;
    font-size: 0.85rem;
}
.ai-hub-msg-user .ai-hub-msg-bubble code {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.ai-hub-msg-bubble pre {
    background: #0f172a;
    color: #f8fafc;
    padding: 12px 14px;
    border-radius: 10px;
    overflow-x: auto;
    direction: ltr;
    text-align: left;
    margin: 10px 0;
}
.ai-hub-msg-bubble pre code {
    background: transparent;
    color: inherit;
    padding: 0;
}
.ai-hub-msg-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
}
.ai-hub-tool-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 0.76rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
}
.ai-hub-tool-btn:hover {
    background: #e2e8f0;
    color: #1e293b;
}

/* مؤشر التفكير في Hub */
.ai-hub-typing {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #ffffff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    color: var(--hub-primary);
    font-size: 0.82rem;
    font-weight: 700;
    align-self: flex-start;
}
.ai-typing-pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--hub-primary);
    animation: typingPulse 1s infinite alternate;
}
@keyframes typingPulse {
    from { opacity: 0.3; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1.2); }
}

/* شريط الصورة المرفقة */
.ai-hub-img-preview-bar {
    padding: 10px 18px;
    background: #fffbeb;
    border-top: 1px solid #fde68a;
    display: flex;
    align-items: center;
    gap: 12px;
}
.ai-hub-img-preview-bar img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #f59e0b;
}
.ai-hub-img-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.ai-hub-img-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #78350f;
}
.ai-hub-img-subtitle {
    font-size: 0.72rem;
    color: #b45309;
}
.ai-hub-remove-img {
    background: transparent;
    border: none;
    color: #b45309;
    font-size: 1.1rem;
    cursor: pointer;
}

/* صندوق الإدخال السفلي في Hub */
.ai-hub-input-box {
    background: #ffffff;
    border-top: 1px solid var(--hub-border);
    padding: 16px 20px 12px;
}
.ai-hub-form-tools {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.ai-tool-btn {
    background: #f8fafc;
    border: 1px solid var(--hub-border);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s;
}
.ai-tool-btn:hover {
    background: #f1f5f9;
    color: var(--hub-primary);
    border-color: #cbd5e1;
}
.ai-tool-btn.active-listening {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
    animation: micPulse 1s infinite alternate;
}
@keyframes micPulse {
    from { transform: scale(1); }
    to { transform: scale(1.05); }
}
.ai-mode-pill {
    margin-inline-start: auto;
    font-size: 0.74rem;
    font-weight: 700;
    color: #475569;
    background: #f8fafc;
    border: 1px solid var(--hub-border);
    border-radius: 999px;
    padding: 4px 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.ai-hub-textarea-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    background: #f8fafc;
    border: 1.5px solid var(--hub-border);
    border-radius: 14px;
    padding: 8px 12px;
    transition: all 0.2s;
}
.ai-hub-textarea-wrapper:focus-within {
    background: #ffffff;
    border-color: #93c5fd;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.ai-hub-textarea {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-family: inherit;
    font-size: 0.92rem;
    color: #0f172a;
    resize: none;
    line-height: 1.5;
    max-height: 160px;
}
.ai-hub-send-btn {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 10px 18px;
    font-size: 0.88rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 3px 10px rgba(29, 78, 216, 0.25);
    transition: all 0.15s;
    flex-shrink: 0;
}
.ai-hub-send-btn:hover {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    transform: translateY(-1px);
}
.ai-hub-disclaimer {
    margin-top: 8px;
    text-align: center;
    font-size: 0.72rem;
    color: #94a3b8;
}

/* شاشات الأجهزة اللوحية والجوال */
@media (max-width: 991px) {
    .ai-hub-grid {
        grid-template-columns: 1fr;
    }
    .ai-hub-sidebar {
        position: static;
        max-height: none;
    }
}
@media (max-width: 640px) {
    .ai-hub-header-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
    }
    .ai-hub-header-actions {
        width: 100%;
        justify-content: flex-start;
    }
    .ai-hub-action-pill {
        flex: 1;
        justify-content: center;
    }
    .ai-hub-messages-box {
        padding: 14px;
    }
    .ai-suggested-topics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// حالة صفحة المركز الموسع
let hubConversationId = null;
let hubSymbolMode = 'ar';
let hubAttachedImageBase64 = null;
let hubIsWaiting = false;
let hubSpeechRec = null;

document.addEventListener('DOMContentLoaded', function() {
    const symbolToggleBtn = document.getElementById('hubSymbolToggleBtn');
    const symbolText = document.getElementById('hubSymbolText');
    const activeModeBadge = document.getElementById('hubActiveModeBadge');
    const mindmapToggleBtn = document.getElementById('hubMindmapToggleBtn');
    const exportPdfBtn = document.getElementById('hubExportPdfBtn');
    const newChatBtn = document.getElementById('hubNewChatBtn');
    const clearAllBtn = document.getElementById('hubClearAllBtn');
    const chatForm = document.getElementById('hubChatForm');
    const promptInput = document.getElementById('hubPromptInput');
    const submitBtn = document.getElementById('hubSubmitBtn');
    const uploadImgBtn = document.getElementById('hubUploadImgBtn');
    const fileInput = document.getElementById('hubFileInput');
    const voiceBtn = document.getElementById('hubVoiceBtn');
    const typingIndicator = document.getElementById('hubTypingIndicator');
    const messagesBox = document.getElementById('hubMessagesBox');

    // 1. تبديل وضع الرموز
    symbolToggleBtn.addEventListener('click', () => {
        if (hubSymbolMode === 'ar') {
            hubSymbolMode = 'en';
            symbolText.textContent = 'Symbols: English (F = ma)';
            activeModeBadge.textContent = 'الرموز: English';
            symbolToggleBtn.style.background = '#eff6ff';
            symbolToggleBtn.style.borderColor = '#93c5fd';
        } else {
            hubSymbolMode = 'ar';
            symbolText.textContent = 'الرموز: عربي (ق = ك × ت)';
            activeModeBadge.textContent = 'الرموز: عربي';
            symbolToggleBtn.style.background = '#ffffff';
            symbolToggleBtn.style.borderColor = '#e2e8f0';
        }
    });

    // 2. خريطة المفاهيم
    mindmapToggleBtn.addEventListener('click', toggleMindmap);

    // 3. تصدير PDF / طباعة
    exportPdfBtn.addEventListener('click', () => {
        window.print();
    });

    // 4. محادثة جديدة
    newChatBtn.addEventListener('click', () => {
        hubConversationId = null;
        removeAttachedImage();
        // إزالة تمييز المحادثات النشطة
        document.querySelectorAll('.ai-conv-item').forEach(el => el.classList.remove('active-conv'));
        // إعادة عرض البنر الترحيبي
        const banner = document.getElementById('hubWelcomeBanner');
        messagesBox.innerHTML = '';
        if (banner) messagesBox.appendChild(banner);
        promptInput.focus();
    });

    // 5. مسح كافة السجلات
    clearAllBtn.addEventListener('click', async () => {
        if (!confirm('هل أنت متأكد من رغبتك في حذف كافة محادثاتك وسجلات الذكاء الاصطناعي؟ لا يمكن التراجع عن هذا الإجراء.')) return;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('ai.conversations.clear') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('hubConversationsList').innerHTML = `
                    <div class="ai-empty-convs" id="hubEmptyConvsNotice">
                        <i class="fa-solid fa-comments"></i>
                        <p>لا توجد محادثات سابقة، ابدأ استفسارك الأول الآن!</p>
                    </div>
                `;
                document.getElementById('hubConvCountBadge').textContent = '0';
                newChatBtn.click();
            }
        } catch (e) {
            alert('حدث خطأ أثناء المسح.');
        }
    });

    // 6. رفع صورة
    uploadImgBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 8 * 1024 * 1024) {
            alert('حجم الصورة كبير جداً، يرجى اختيار صورة أصغر من 8 ميجابايت.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            hubAttachedImageBase64 = event.target.result;
            document.getElementById('hubAttachedImg').src = hubAttachedImageBase64;
            document.getElementById('hubImagePreviewBar').style.display = 'flex';
        };
        reader.readAsDataURL(file);
    });

    // 7. إدخال صوتي
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        hubSpeechRec = new SpeechRec();
        hubSpeechRec.lang = 'ar-SA';
        hubSpeechRec.continuous = false;

        hubSpeechRec.onstart = () => {
            voiceBtn.classList.add('active-listening');
            promptInput.placeholder = 'جارٍ الاستماع إليك... تحدث الآن';
        };
        hubSpeechRec.onresult = (e) => {
            const transcript = e.results[0][0].transcript;
            promptInput.value = (promptInput.value ? promptInput.value + ' ' : '') + transcript;
            autoResizeHubTextarea();
        };
        hubSpeechRec.onerror = () => {
            voiceBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اكتب سؤالك هنا بالتفصيل...';
        };
        hubSpeechRec.onend = () => {
            voiceBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اكتب سؤالك هنا بالتفصيل...';
        };

        voiceBtn.addEventListener('click', () => {
            try {
                if (voiceBtn.classList.contains('active-listening')) {
                    hubSpeechRec.stop();
                } else {
                    hubSpeechRec.start();
                }
            } catch (err) {
                console.warn('Speech error:', err);
            }
        });
    } else {
        voiceBtn.style.opacity = '0.5';
        voiceBtn.title = 'خاصية الإملاء الصوتي غير مدعومة في هذا المتصفح';
    }

    // 8. ضبط حقل الإدخال
    promptInput.addEventListener('input', autoResizeHubTextarea);
    function autoResizeHubTextarea() {
        promptInput.style.height = 'auto';
        promptInput.style.height = Math.min(promptInput.scrollHeight, 160) + 'px';
    }

    promptInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendHubMessage();
        }
    });

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        sendHubMessage();
    });
});

// إرسال سؤال سريع مقترح
function sendQuickPrompt(promptText) {
    const input = document.getElementById('hubPromptInput');
    input.value = promptText;
    sendHubMessage();
}

// تبديل الخريطة الذهنية
function toggleMindmap() {
    const drawer = document.getElementById('hubMindmapDrawer');
    drawer.style.display = (drawer.style.display === 'none') ? 'block' : 'none';
}

// إلغاء الصورة المرفقة
function removeAttachedImage() {
    hubAttachedImageBase64 = null;
    document.getElementById('hubFileInput').value = '';
    document.getElementById('hubImagePreviewBar').style.display = 'none';
}

// إرسال الرسالة إلى السيرفر في Hub
async function sendHubMessage() {
    if (hubIsWaiting) return;

    const input = document.getElementById('hubPromptInput');
    const text = input.value.trim();
    const image = hubAttachedImageBase64;

    if (!text && !image) return;

    // إخفاء بنر الترحيب إن كان ظاهراً
    const welcomeBanner = document.getElementById('hubWelcomeBanner');
    if (welcomeBanner) welcomeBanner.style.display = 'none';

    // إضافة رسالة الطالب للواجهة
    appendHubUserMessage(text, image);

    input.value = '';
    input.style.height = 'auto';
    removeAttachedImage();

    hubIsWaiting = true;
    const typingIndicator = document.getElementById('hubTypingIndicator');
    typingIndicator.style.display = 'flex';
    scrollHubBottom();

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch("{{ route('ai.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prompt: text,
                conversation_id: hubConversationId,
                symbol_mode: hubSymbolMode,
                image_base64: image
            })
        });

        const data = await res.json();

        if (res.status === 401 && data.require_login) {
            appendHubAssistantMessage('⚠️ ' + data.message);
            return;
        }

        if (data.success) {
            hubConversationId = data.conversation_id;
            appendHubAssistantMessage(data.reply, data.provider);
            // تحديث أو إضافة عنوان المحادثة في القائمة الجانبية إن كانت جديدة
            updateSidebarList(data.conversation_id, data.conversation_title, data.symbol_mode);
        } else {
            appendHubAssistantMessage('عذراً، حدث خطأ: ' + (data.message || 'يرجى المحاولة مجدداً'));
        }
    } catch (error) {
        console.error('Hub AI Error:', error);
        appendHubAssistantMessage('⚠️ تعذر الاتصال بمحرك الذكاء الاصطناعي حالياً. يرجى التأكد من اتصال الإنترنت والمحاولة مرة أخرى.');
    } finally {
        hubIsWaiting = false;
        typingIndicator.style.display = 'none';
        scrollHubBottom();
    }
}

// إضافة رسالة المستخدم في Hub
function appendHubUserMessage(text, image) {
    const box = document.getElementById('hubMessagesBox');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-hub-msg ai-hub-msg-user';

    let contentHtml = '';
    if (image) {
        contentHtml += `<div style="margin-bottom: 8px;"><img src="${image}" style="max-width: 240px; max-height: 180px; border-radius: 10px; border: 1.5px solid rgba(255,255,255,0.4);"></div>`;
    }
    if (text) {
        contentHtml += `<div>${escapeHtml(text).replace(/\n/g, '<br>')}</div>`;
    }

    msgDiv.innerHTML = `
        <div class="ai-hub-msg-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="ai-hub-msg-content">
            <div class="ai-hub-msg-meta">
                <span>أنت</span>
                <span>&bull; ${getNowTimeString()}</span>
            </div>
            <div class="ai-hub-msg-bubble">
                ${contentHtml}
            </div>
        </div>
    `;
    box.appendChild(msgDiv);
    scrollHubBottom();
}

// إضافة رد الذكاء الاصطناعي في Hub مع دعم KaTeX و Markdown
function appendHubAssistantMessage(rawText, provider) {
    const box = document.getElementById('hubMessagesBox');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-hub-msg ai-hub-msg-assistant';

    let formattedHtml = rawText;
    if (typeof marked !== 'undefined') {
        try {
            formattedHtml = marked.parse(rawText);
        } catch (e) {
            formattedHtml = escapeHtml(rawText).replace(/\n/g, '<br>');
        }
    } else {
        formattedHtml = escapeHtml(rawText).replace(/\n/g, '<br>');
    }

    const uniqueId = 'hubAi_' + Date.now();

    msgDiv.innerHTML = `
        <div class="ai-hub-msg-avatar">
            <i class="fa-solid fa-brain"></i>
        </div>
        <div class="ai-hub-msg-content">
            <div class="ai-hub-msg-meta">
                <span>المعلم الأكاديمي الذكي</span>
                <span>&bull; ${getNowTimeString()}</span>
            </div>
            <div class="ai-hub-msg-bubble" id="${uniqueId}">
                ${formattedHtml}
            </div>
            <div class="ai-hub-msg-actions">
                <button type="button" class="ai-hub-tool-btn" onclick="copyHubAiText('${uniqueId}')">
                    <i class="fa-regular fa-copy"></i> نسخ الإجابة
                </button>
                <button type="button" class="ai-hub-tool-btn" onclick="speakHubAiText('${uniqueId}')">
                    <i class="fa-solid fa-volume-high"></i> قراءة صوتية
                </button>
            </div>
        </div>
    `;

    box.appendChild(msgDiv);

    // تصيير معادلات KaTeX
    renderMathSafe(msgDiv.querySelector('.ai-hub-msg-bubble'));

    scrollHubBottom();
}

// تصيير معادلات KaTeX
function renderMathSafe(elem) {
    if (typeof renderMathInElement !== 'undefined' && elem) {
        try {
            renderMathInElement(elem, {
                delimiters: [
                    { left: '$$', right: '$$', display: true },
                    { left: '$', right: '$', display: false },
                    { left: '\\(', right: '\\)', display: false },
                    { left: '\\[', right: '\\]', display: true }
                ],
                throwOnError: false
            });
        } catch (err) {
            console.warn('KaTeX render note:', err);
        }
    }
}

// تحميل محادثة سابقة من السيرفر
async function loadConversation(id) {
    hubConversationId = id;

    // تمييز المحادثة في القائمة
    document.querySelectorAll('.ai-conv-item').forEach(el => {
        el.classList.toggle('active-conv', el.getAttribute('data-id') == id);
    });

    const box = document.getElementById('hubMessagesBox');
    box.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 1.8rem; color: var(--hub-primary);"></i>
            <p style="margin-top: 10px; font-weight: 700;">جارٍ استرجاع سجل المحادثة...</p>
        </div>
    `;

    try {
        const res = await fetch("{{ url('/ai/conversations') }}/" + id);
        const data = await res.json();

        box.innerHTML = '';

        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                if (msg.role === 'user') {
                    appendHubUserMessage(msg.content, null);
                } else {
                    appendHubAssistantMessage(msg.content, null);
                }
            });
        } else {
            box.innerHTML = `<p style="text-align: center; color: #94a3b8; padding: 30px;">المحادثة فارغة، اسأل سؤالك الآن!</p>`;
        }
    } catch (e) {
        box.innerHTML = `<p style="text-align: center; color: #ef4444; padding: 30px;">تعذر تحميل رسائل المحادثة.</p>`;
    }
}

// حذف محادثة
async function deleteConversation(id, event) {
    event.stopPropagation();
    if (!confirm('هل تريد حذف هذه المحادثة؟')) return;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch("{{ url('/ai/conversations') }}/" + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (data.success) {
            const item = document.querySelector(`.ai-conv-item[data-id="${id}"]`);
            if (item) item.remove();
            if (hubConversationId == id) {
                document.getElementById('hubNewChatBtn').click();
            }
        }
    } catch (e) {
        alert('تعذر حذف المحادثة.');
    }
}

// تحديث القائمة الجانبية عند إنشاء محادثة جديدة
function updateSidebarList(id, title, symbolMode) {
    const list = document.getElementById('hubConversationsList');
    const existing = document.querySelector(`.ai-conv-item[data-id="${id}"]`);
    if (existing) {
        existing.classList.add('active-conv');
        return;
    }

    const emptyNotice = document.getElementById('hubEmptyConvsNotice');
    if (emptyNotice) emptyNotice.remove();

    const newItem = document.createElement('div');
    newItem.className = 'ai-conv-item active-conv';
    newItem.setAttribute('data-id', id);
    newItem.innerHTML = `
        <div class="ai-conv-item-main" onclick="loadConversation('${id}')">
            <i class="fa-regular fa-message ai-conv-icon" style="color: var(--hub-primary);"></i>
            <div class="ai-conv-details">
                <span class="ai-conv-title" title="${escapeHtml(title)}">${escapeHtml(title)}</span>
                <span class="ai-conv-date">
                    الآن &bull; <span class="ai-notation-tag">${symbolMode === 'en' ? 'EN' : 'عربي'}</span>
                </span>
            </div>
        </div>
        <button type="button" class="ai-conv-del-btn" title="حذف" onclick="deleteConversation('${id}', event)">
            <i class="fa-regular fa-trash-can"></i>
        </button>
    `;

    list.insertBefore(newItem, list.firstChild);

    const countBadge = document.getElementById('hubConvCountBadge');
    if (countBadge) {
        const currentCount = parseInt(countBadge.textContent) || 0;
        countBadge.textContent = currentCount + 1;
    }
}

function scrollHubBottom() {
    const box = document.getElementById('hubMessagesBox');
    box.scrollTop = box.scrollHeight;
}

function getNowTimeString() {
    const d = new Date();
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function copyHubAiText(elemId) {
    const el = document.getElementById(elemId);
    if (!el) return;
    const text = el.innerText || el.textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('تم نسخ الإجابة بنجاح ✅');
    });
}

function speakHubAiText(elemId) {
    const el = document.getElementById(elemId);
    if (!el) return;
    const text = el.innerText || el.textContent;
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'ar-SA';
        utterance.rate = 0.95;
        window.speechSynthesis.speak(utterance);
    }
}
</script>
@endsection
