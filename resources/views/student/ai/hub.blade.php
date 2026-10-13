@extends('layouts.app')

@section('title', __('Step by Step Ai | المساعد الأكاديمي الذكي') . ' - ' . config('app.name', 'Step by Step'))

@section('content')
<div class="step-ai-container" id="stepAiContainer" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    <!-- ========================================================================= -->
    <!-- 1. الشريط الجانبي (Sidebar) للمحادثات وتاريخ الجلسات                        -->
    <!-- ========================================================================= -->
    <aside class="step-ai-sidebar" id="stepAiSidebar">
        
        <!-- ترويسة الشريط الجانبي: الشعار واسم المساعد -->
        <div class="sidebar-brand-box">
            <a href="{{ route('home') }}" class="brand-link" title="{{ __('العودة للرئيسية') }}">
                <img src="{{ asset('logo.png') }}" alt="Step by Step" class="brand-logo-img" onerror="this.src='{{ asset('images/logo.png') }}'">
                <div class="brand-titles">
                    <span class="brand-name">Step by Step</span>
                    <span class="brand-sub">Smart Ai Assistant</span>
                </div>
            </a>
            <button type="button" class="btn-sidebar-close-mobile" id="mobileSidebarCloseBtn" title="{{ __('إغلاق القائمة') }}" aria-label="{{ __('إغلاق') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- زر بدء محادثة جديدة -->
        <div class="sidebar-action-header">
            <button type="button" class="btn-new-chat" id="stepNewChatBtn" title="{{ __('بدء محادثة جديدة (Ctrl+K)') }}">
                <i class="fa-solid fa-plus"></i>
                <span>{{ __('محادثة جديدة') }}</span>
                <kbd class="shortcut-tag">Ctrl+K</kbd>
            </button>
        </div>

        <!-- صندوق البحث الفوري في المحادثات -->
        <div class="sidebar-search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="convSearchInput" class="conv-search-input" placeholder="{{ __('بحث في المحادثات...') }}" autocomplete="off">
            <button type="button" id="convSearchClearBtn" class="btn-search-clear" style="display: none;" title="{{ __('مسح') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- قائمة المحادثات السابقة مصنفة زمنياً -->
        <div class="sidebar-conversations-section">
            <div class="section-label-row">
                <span class="section-label">{{ __('المحادثات السابقة') }}</span>
                <span class="conv-count-badge" id="stepConvCountBadge">{{ count($conversations) }}</span>
            </div>

            <div class="conversations-scroll-list" id="stepConversationsList">
                @php
                    $hasAny = !empty($groupedConversations['today']) || !empty($groupedConversations['yesterday']) || !empty($groupedConversations['previous_7_days']) || !empty($groupedConversations['older']);
                @endphp

                @if($hasAny)
                    {{-- اليوم --}}
                    @if(!empty($groupedConversations['today']))
                        <div class="date-group-heading">{{ __('اليوم') }}</div>
                        @foreach($groupedConversations['today'] as $c)
                            @include('student.ai.partials.conv_item', ['conv' => $c])
                        @endforeach
                    @endif

                    {{-- أمس --}}
                    @if(!empty($groupedConversations['yesterday']))
                        <div class="date-group-heading">{{ __('أمس') }}</div>
                        @foreach($groupedConversations['yesterday'] as $c)
                            @include('student.ai.partials.conv_item', ['conv' => $c])
                        @endforeach
                    @endif

                    {{-- آخر 7 أيام --}}
                    @if(!empty($groupedConversations['previous_7_days']))
                        <div class="date-group-heading">{{ __('آخر 7 أيام') }}</div>
                        @foreach($groupedConversations['previous_7_days'] as $c)
                            @include('student.ai.partials.conv_item', ['conv' => $c])
                        @endforeach
                    @endif

                    {{-- الأقدم --}}
                    @if(!empty($groupedConversations['older']))
                        <div class="date-group-heading">{{ __('السابقة') }}</div>
                        @foreach($groupedConversations['older'] as $c)
                            @include('student.ai.partials.conv_item', ['conv' => $c])
                        @endforeach
                    @endif
                @else
                    <div class="empty-conversations-state" id="stepEmptyConvsNotice">
                        <div class="empty-icon-wrap">
                            <i class="fa-regular fa-comments"></i>
                        </div>
                        <p>{{ __('لا توجد محادثات سابقة، ابدأ استفسارك الأول الآن!') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- أسفل الشريط الجانبي: هوية المستخدم والأدوات -->
        <div class="sidebar-footer">
            <div class="user-profile-strip">
                <div class="user-avatar-circle">
                    @if(!empty($actor['avatar']))
                        <img src="{{ $actor['avatar'] }}" alt="{{ $actor['name'] }}" class="user-avatar-img">
                    @else
                        {{ mb_substr($actor['name'] ?? 'U', 0, 1, 'UTF-8') }}
                    @endif
                </div>
                <div class="user-strip-info">
                    <span class="user-strip-name">{{ $actor['name'] ?? __('مستخدم المنصة') }}</span>
                    <span class="user-strip-role">
                        @if($actor['role'] === 'admin')
                            <i class="fa-solid fa-crown text-amber"></i> {{ __('المدير العام') }}
                        @elseif($actor['role'] === 'teacher')
                            <i class="fa-solid fa-graduation-cap text-blue"></i> {{ __('معلم المبحث') }}
                        @elseif($actor['role'] === 'videographer')
                            <i class="fa-solid fa-video text-purple"></i> {{ __('المصور الأكاديمي') }}
                        @elseif($actor['role'] === 'student')
                            <i class="fa-solid fa-circle-check text-emerald"></i> {{ __('طالب المنصة') }}
                        @else
                            <i class="fa-solid fa-globe text-cyan"></i> {{ __('زائر المنصة') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="footer-actions-row">
                <button type="button" class="btn-footer-tool" id="stepSettingsBtn" title="{{ __('الإعدادات') }}" aria-label="{{ __('الإعدادات') }}">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <button type="button" class="btn-footer-tool" id="stepThemeToggleBtn" title="{{ __('تبديل المظهر (فاتح / داكن)') }}" aria-label="{{ __('المظهر') }}">
                    <i class="fa-solid fa-moon theme-icon"></i>
                </button>
                <button type="button" class="btn-footer-tool btn-danger-action" id="stepClearAllBtn" title="{{ __('مسح كافة سجلات المحادثات') }}" aria-label="{{ __('مسح السجل') }}">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
                <a href="{{ route('home') }}" class="btn-footer-tool" title="{{ __('العودة للرئيسية') }}" aria-label="{{ __('الرئيسية') }}">
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>

    </aside>

    <!-- غطاء خلفية الموبايل (Drawer Backdrop) -->
    <div class="sidebar-mobile-backdrop" id="sidebarMobileBackdrop"></div>

    <!-- ========================================================================= -->
    <!-- 2. منطقة المحادثة الرئيسية (Main Chat Workspace)                         -->
    <!-- ========================================================================= -->
    <main class="step-ai-main">

        <!-- الترويسة العلوية الفاخرة للدردشة -->
        <header class="chat-top-header">
            <div class="header-left-group">
                <!-- زر إظهار/إخفاء الشريط الجانبي -->
                <button type="button" class="btn-toggle-sidebar" id="sidebarToggleBtn" title="{{ __('إظهار / إخفاء القائمة الجانبية') }}" aria-label="{{ __('القائمة') }}">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>

                <div class="brand-title-wrap">
                    <img src="{{ asset('logo.png') }}" alt="Step by Step" class="header-logo-icon" onerror="this.src='{{ asset('images/logo.png') }}'">
                    <div class="brand-text-column">
                        <div class="brand-name-row">
                            <h1 class="brand-title">Step by Step Ai</h1>
                            
                            <!-- قائمة اختيار النموذج الذكي -->
                            <div class="model-selector-dropdown" id="modelSelectorDropdown">
                                <button type="button" class="model-badge-btn" id="modelSelectorBtn" title="{{ __('تغيير نموذج الذكاء الاصطناعي') }}">
                                    <i class="fa-solid fa-bolt-lightning model-icon"></i>
                                    <span id="activeModelLabel">Smart 3.8 Flash</span>
                                    <i class="fa-solid fa-chevron-down caret-icon"></i>
                                </button>
                                <div class="model-dropdown-menu" id="modelDropdownMenu">
                                    <div class="model-option active" data-model="smart">
                                        <div class="model-option-info">
                                            <span class="model-name">Step by Step Smart</span>
                                            <span class="model-desc">Gemini 3.8 Flash • فائق الذكاء والدقة والسرعة (موصى به)</span>
                                        </div>
                                        <i class="fa-solid fa-check check-icon"></i>
                                    </div>
                                    <div class="model-option" data-model="fast">
                                        <div class="model-option-info">
                                            <span class="model-name">Fast Lite</span>
                                            <span class="model-desc">3.8 Flash Lite • سرعة فائقة للاستفسارات السريعة</span>
                                        </div>
                                        <i class="fa-solid fa-check check-icon"></i>
                                    </div>
                                    <div class="model-option" data-model="balanced">
                                        <div class="model-option-info">
                                            <span class="model-name">Balanced Core</span>
                                            <span class="model-desc">Gemini 3.5 Core • متوازن للدراسة العامة والشروحات</span>
                                        </div>
                                        <i class="fa-solid fa-check check-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="brand-tagline">{{ __('المساعد الأكاديمي والتعليمي الذكي لمنصة Step by Step') }}</span>
                    </div>
                </div>
            </div>

            <div class="header-right-group">
                <!-- شريط المحادثة النشطة -->
                <div class="active-conv-pill" id="stepActiveConvPill">
                    <i class="fa-regular fa-comment-dots active-conv-icon"></i>
                    <span class="active-conv-title" id="stepActiveConvTitle" title="{{ __('محادثة جديدة') }}">{{ __('محادثة جديدة') }}</span>
                    <button type="button" class="btn-pill-action" id="stepRenameCurrentBtn" title="{{ __('تسمية وحفظ المحادثة') }}" onclick="openRenameModalForCurrent()">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn-pill-action btn-pill-danger" id="stepDeleteCurrentBtn" title="{{ __('حذف هذه المحادثة') }}" onclick="openDeleteModalForCurrent()" style="display: none;">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>

                <!-- زر تبديل لغة الرموز الرياضية (عربي / إنجليزي) -->
                <button type="button" class="btn-symbol-mode-toggle" id="stepSymbolToggleBtn" title="{{ __('تبديل لغة الرموز الرياضية والعلمية') }}">
                    <i class="fa-solid fa-square-root-variable"></i>
                    <span id="stepSymbolText">الرموز: عربي (ق=ك×ت)</span>
                </button>

                <!-- زر تصدير المحادثة PDF / طباعة -->
                <button type="button" class="btn-header-tool" id="stepExportPdfBtn" title="{{ __('تصدير أو طباعة المحادثة PDF') }}">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span class="tool-label-text">{{ __('تصدير') }}</span>
                </button>
            </div>
        </header>

        <!-- منطقة الرسائل المركزية (Feed) -->
        <div class="chat-messages-container" id="stepMessagesBox">

            <!-- شاشة الترحيب الافتراضية العصرية المخصصة لكل دور -->
            <div class="chat-welcome-hero" id="stepWelcomeBanner">
                <div class="welcome-logo-badge">
                    <img src="{{ asset('logo.png') }}" alt="Step by Step Ai" class="welcome-brand-logo" onerror="this.src='{{ asset('images/logo.png') }}'">
                </div>

                <h2 class="welcome-heading">
                    @if($actor['role'] === 'teacher')
                        {{ __('أهلاً بك يا أستاذ :name في Step by Step Ai!', ['name' => $actor['name']]) }}
                    @elseif($actor['role'] === 'admin')
                        {{ __('مرحباً بإدارة المنصة! كيف يمكن لمساعد Step by Step Ai خدمتك؟') }}
                    @elseif($actor['role'] === 'videographer')
                        {{ __('أهلاً بك في استوديو المساعد الذكي لمصوري المنصة!') }}
                    @else
                        {{ __('أهلاً بك يا :name في Step by Step Ai!', ['name' => $actor['name']]) }}
                    @endif
                </h2>

                <p class="welcome-subheading">
                    @if($actor['role'] === 'teacher')
                        {{ __('مساعدك الذكي لإعداد الاختبارات الوزارية، خطط الدروس، سلالم التصحيح، وتلخيص المقررات الدراسية بأعلى جودة.') }}
                    @elseif($actor['role'] === 'admin')
                        {{ __('مساعدك لصياغة التعاميم الأكاديمية، تنظيم الأنشطة التعليمية، وتطوير تجربة المنصة للطلاب والمعلمين.') }}
                    @elseif($actor['role'] === 'videographer')
                        {{ __('مساعدك لكتابة نصوص المحاضرات (Scripts)، تنظيم الدروس في فصول ذكية، وضبط نصائح الإضاءة والمونتاج التعليمي.') }}
                    @else
                        {{ __('اسأل عن أي مسألة، قانون، إعراب، شرح درس، كود برمجي، أو استفسار عن خدمات المنصة وحاسبة المعدل — خطوة بخطوة بالرموز المطلوبة.') }}
                    @endif
                </p>

                <!-- شريط الأدوات التعليمية السريعة (Educational Quick Actions) -->
                <div class="edu-tools-strip">
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('explain')">
                        <i class="fa-solid fa-chalkboard-user"></i> <span>اشرح خطوة بخطوة</span>
                    </button>
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('solve')">
                        <i class="fa-solid fa-calculator"></i> <span>حل مسألة بالتفصيل</span>
                    </button>
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('summarize')">
                        <i class="fa-solid fa-file-lines"></i> <span>تلخيص ونقاط رئيسية</span>
                    </button>
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('quiz')">
                        <i class="fa-solid fa-list-check"></i> <span>أنشئ لي اختباراً</span>
                    </button>
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('code')">
                        <i class="fa-solid fa-code"></i> <span>كتابة وتصحيح كود</span>
                    </button>
                    <button type="button" class="edu-tool-pill" onclick="triggerQuickAction('translate')">
                        <i class="fa-solid fa-language"></i> <span>ترجمة أكاديمية</span>
                    </button>
                </div>

                <!-- شبكة البطاقات السريعة المقترحة حسب الدور -->
                <div class="quick-prompts-grid">
                    @if($actor['role'] === 'teacher')
                        <div class="prompt-card" onclick="sendQuickPrompt('أنشئ لي نموذج امتحان وزاري مكون من 5 أسئلة اختيار من متعدد وسؤالين مقاليين مع الإجابة النموذجية وسلم التصحيح')">
                            <div class="prompt-card-icon icon-blue"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('بناء امتحان وزاري نموذجي') }}</h4>
                                <p>{{ __('أسئلة اختيار من متعدد ومقالية مع الإجابة وسلالم التصحيح.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('ساعدني في إعداد خطة تحضير درس تفاعلي منظم مع أهداف تعليمية وأنشطة ممتعة للطلاب')">
                            <div class="prompt-card-icon icon-emerald"><i class="fa-solid fa-book-bookmark"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('خطة تحضير درس نموذجي') }}</h4>
                                <p>{{ __('أهداف سلوكية، تمهيد تشويقي، وأنشطة تعليمية وتقويم ختامي.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('لخص لي هذا الفصل الدراسي في نقاط مكثفة مع أهم القوانين والمصطلحات لتوزيعها كدوسية مراجعة للطلاب')">
                            <div class="prompt-card-icon icon-amber"><i class="fa-solid fa-layer-group"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('إعداد ملخص ودوسية مراجعة') }}</h4>
                                <p>{{ __('أهم القوانين والتعريفات والأسئلة الشائعة في المنهاج الوزاري.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('كيف أشرح المفاهيم المعقدة في هذا الدرس بطريقة مبسطة مع أمثلة من واقع الحياة للطلاب؟')">
                            <div class="prompt-card-icon icon-purple"><i class="fa-solid fa-lightbulb"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('تبسيط المفاهيم الصعبة') }}</h4>
                                <p>{{ __('استراتيجيات تعليمية وأمثلة واقعية تجعل الدرس راسخاً وممتعاً.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                    @elseif($actor['role'] === 'videographer')
                        <div class="prompt-card" onclick="sendQuickPrompt('اكتب لي نص مقدمة وخاتمة (Script) جذاب واحترافي لتصوير محاضرة تعليمية جديدة للمنصة')">
                            <div class="prompt-card-icon icon-purple"><i class="fa-solid fa-video"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('نص وسيناريو تصوير محاضرة') }}</h4>
                                <p>{{ __('مقدمة تشويقية، فواصل، وخاتمة تحفيزية للطلاب.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('كيف أقسم محاضرة مدتها ساعة ونصف إلى فصول تعليمية (Video Chapters) مع عناوين جذابة؟')">
                            <div class="prompt-card-icon icon-blue"><i class="fa-solid fa-list-ol"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('تقسيم الفيديو لفصول ذكية') }}</h4>
                                <p>{{ __('تجزئة المحتوى الطويل إلى مقاطع مريحة مع عناوين وأوقات زمنية.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('ما هي أفضل ممارسات ضبط الإضاءة والصوت والميكروفون لتسجيل شرح المعلم على الشاشة الذكية؟')">
                            <div class="prompt-card-icon icon-amber"><i class="fa-solid fa-sliders"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('ضبط الصوت وإضاءة الاستوديو') }}</h4>
                                <p>{{ __('نصائح منع انعكاسات الشاشة الذكية وضمان وضوح الصوت والنقاء.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('أفكار لتصميم غلاف فيديو (Thumbnail) جذاب ومحفز للدرس التعليمي مع عناصر التصميم')">
                            <div class="prompt-card-icon icon-emerald"><i class="fa-solid fa-image"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('أفكار وتصميم أغلفة الفيديوهات') }}</h4>
                                <p>{{ __('عناصر بصرية وعبارات جذابة تشجع الطالب على مشاهدة المحاضرة.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                    @elseif($actor['role'] === 'admin')
                        <div class="prompt-card" onclick="sendQuickPrompt('صياغة إعلان رسمي تحفيزي لطلاب المنصة بمناسبة بدء الفصل الدراسي الجديد والتسجيل بالدورات')">
                            <div class="prompt-card-icon icon-blue"><i class="fa-solid fa-bullhorn"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('صياغة إعلانات وتعاميم رسمية') }}</h4>
                                <p>{{ __('إعلانات رسمية بأسلوب راقٍ ومشجع للطلبة وأولياء الأمور.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('اقتراحات لتنظيم أسبوع الامتحانات التجريبية في المنصة وتنسيق الدعم الفني والأكاديمي')">
                            <div class="prompt-card-icon icon-purple"><i class="fa-solid fa-calendar-check"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('تنظيم فعاليات الامتحانات') }}</h4>
                                <p>{{ __('خطة إدارة فترات الاختبارات ورفع معنويات الطلبة.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('ما هي أهم مميزات منصة Step by Step وكيف تشرحها للزوار والطلبة الجدد بطريقة احترافية؟')">
                            <div class="prompt-card-icon icon-emerald"><i class="fa-solid fa-circle-info"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('دليل ومزايا منصة Step by Step') }}</h4>
                                <p>{{ __('شرح حاسبة التوجيهي، ميزة الأوفلاين، وبنك الامتحانات.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('أفكار لتكريم وتكليل جهود الطلبة المتفوقين في المنصة وتعزيز ارتباطهم التعليمي')">
                            <div class="prompt-card-icon icon-amber"><i class="fa-solid fa-award"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('برامج تكريم المتفوقين') }}</h4>
                                <p>{{ __('أفكار شهادات التقدير، لوحة الشرف، والتحفيز المستمر.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                    @else
                        {{-- وضع الطالب والزائر --}}
                        <div class="prompt-card" onclick="sendQuickPrompt('اشرح لي قانون كولوم والمجال الكهربائي في الفيزياء مع الصيغة بالرموز وحل مسألة عليه')">
                            <div class="prompt-card-icon icon-blue"><i class="fa-solid fa-atom"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('قانون كولوم والمجال الكهربائي') }}</h4>
                                <p>{{ __('شرح فيزيائي مع الرموز ووحدات القياس ومسألة تطبيقية.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('اشرح لي قواعد كان وأخواتها وإن وأخواتها مع نماذج إعرابية كاملة لدروس المنهاج')">
                            <div class="prompt-card-icon icon-emerald"><i class="fa-solid fa-feather-pointed"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('قواعد النحو والإعراب') }}</h4>
                                <p>{{ __('الأفعال والحروف الناسخة، دلالاتها، ونماذج إعرابية تفصيلية.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('ما هي قواعد التكامل بالتعويض والتكامل بالأجزاء مع أمثلة محلولة خطوة بخطوة بالرموز؟')">
                            <div class="prompt-card-icon icon-amber"><i class="fa-solid fa-infinity"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('طرق التفاضل والتكامل') }}</h4>
                                <p>{{ __('التكامل بالتعويض والأجزاء، مشتقات الاقترانات الدائرية.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                        <div class="prompt-card" onclick="sendQuickPrompt('كيف أحسب معدلي التوجيهي الوزاري بدقة وما هي ميزات منصة Step by Step الأكاديمية؟')">
                            <div class="prompt-card-icon icon-purple"><i class="fa-solid fa-calculator"></i></div>
                            <div class="prompt-card-text">
                                <h4>{{ __('حساب المعدل ودليل المنصة') }}</h4>
                                <p>{{ __('حاسبة المعدل الوزارية، ميزة الأوفلاين، وبنك الامتحانات.') }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-left prompt-card-arrow"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- مؤشر جاري الكتابة والتفكير (Thinking / Typing Wave) -->
            <div class="chat-typing-row" id="stepTypingIndicator" style="display: none;">
                <div class="ai-avatar-tiny">
                    <img src="{{ asset('logo.png') }}" alt="AI" onerror="this.src='{{ asset('images/logo.png') }}'">
                </div>
                <div class="typing-bubble">
                    <span class="typing-text" id="typingTextNotice">{{ __('Step by Step Ai يقوم بالتحليل والصياغة...') }}</span>
                    <div class="typing-wave-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- زر عائم لإيقاف التوليد أثناء البث (Stop Generating) -->
        <div class="stop-generating-dock" id="stopGeneratingDock" style="display: none;">
            <button type="button" class="btn-stop-generating" id="btnStopGenerating">
                <i class="fa-solid fa-circle-stop"></i>
                <span>{{ __('إيقاف التوليد (Stop)') }}</span>
            </button>
        </div>

        <!-- شريط معاينة المرفق (صورة أو ملف PDF / مستند) قبل الإرسال -->
        <div class="attached-preview-bar" id="stepAttachmentBar" style="display: none;">
            <div class="attached-file-badge">
                <div class="attached-icon-box" id="attachedIconBox">
                    <i class="fa-solid fa-file" id="attachedIcon"></i>
                    <img id="attachedThumbImg" src="" alt="{{ __('معاينة') }}" style="display: none;">
                </div>
                <div class="attached-details">
                    <span class="attached-name" id="attachedFileName">{{ __('اسم الملف') }}</span>
                    <span class="attached-size" id="attachedFileSize">{{ __('الحجم') }}</span>
                </div>
                <button type="button" class="btn-remove-attachment" onclick="removeCurrentAttachment()" title="{{ __('إلغاء المرفق') }}" aria-label="{{ __('حذف') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. شريط الإدخال العائم الفاخر (ChatGPT Modern Input Dock)                 -->
        <!-- ========================================================================= -->
        <footer class="chat-input-dock">
            <form id="stepChatForm" class="chat-input-form" onsubmit="return false;">
                
                <!-- حقل إدخال الملفات الخفي (صور ومستندات) -->
                <input type="file" id="stepFileInput" accept="image/*,.pdf,.doc,.docx,.txt,.csv,.json,.py,.js,.php,.html,.css,.md" style="display: none;">

                <div class="dock-actions-start">
                    <!-- زر إرفاق ملف / صورة -->
                    <button type="button" class="dock-tool-btn" id="stepUploadBtn" title="{{ __('إرفاق صورة مسألة، صفحة كتاب، أو ملف PDF/مستند') }}" aria-label="{{ __('إرفاق ملف') }}">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>

                    <!-- زر الإملاء الصوتي بالميكروفون -->
                    <button type="button" class="dock-tool-btn" id="stepVoiceBtn" title="{{ __('التحدث بالصوت (إملاء صوتي)') }}" aria-label="{{ __('ميكروفون') }}">
                        <i class="fa-solid fa-microphone"></i>
                    </button>
                </div>

                <!-- حقل الإدخال النصي الذكي القابل للتمدد -->
                <textarea id="stepPromptInput" class="dock-textarea" rows="1" placeholder="{{ __('اسأل عن أي شيء، مسألة فيزياء، معادلة، إعراب، شرح درس، كود، أو مستند...') }}" maxlength="8000"></textarea>

                <div class="dock-actions-end">
                    <!-- زر الإرسال -->
                    <button type="submit" id="stepSubmitBtn" class="btn-dock-send" title="{{ __('إرسال الرسالة (Enter)') }}" disabled aria-label="{{ __('إرسال') }}">
                        <i class="fa-solid fa-arrow-up"></i>
                    </button>
                </div>
            </form>

            <div class="dock-bottom-note">
                <span>منظومة <strong>Step by Step Ai</strong> &bull; نموذج رسمي مدعوم &bull; استخدم <strong>Shift + Enter</strong> لإنشاء سطر جديد</span>
            </div>
        </footer>

    </main>

</div>

<!-- ========================================================================= -->
<!-- نافذة تعديل وتسمية المحادثة (Rename Modal)                                -->
<!-- ========================================================================= -->
<div class="ai-modal-overlay" id="stepRenameModalOverlay">
    <div class="ai-modal-card">
        <div class="ai-modal-header">
            <div class="modal-header-title">
                <div class="modal-icon-badge">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 id="renameModalTitleText">{{ __('تسمية وحفظ المحادثة') }}</h3>
                    <p class="modal-subtitle">{{ __('اختر اسماً يسهل عليك الرجوع إلى هذه المحادثة في أي وقت') }}</p>
                </div>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeRenameModal()">&times;</button>
        </div>
        <div class="ai-modal-body">
            <label for="renameModalInput" class="modal-input-label">{{ __('اسم المحادثة:') }}</label>
            <input type="text" id="renameModalInput" class="modal-text-input" placeholder="{{ __('مثال: مسائل الفيزياء، شرح الإعلال...') }}" maxlength="150" autocomplete="off">
            <div class="modal-quick-suggestions">
                <span class="suggestion-chip" onclick="setSuggestedTitle('حل مسائل رياضيات توجيهي')">رياضيات توجيهي</span>
                <span class="suggestion-chip" onclick="setSuggestedTitle('قوانين ومسائل الفيزياء')">مسائل فيزياء</span>
                <span class="suggestion-chip" onclick="setSuggestedTitle('مراجعة قواعد اللغة العربية')">قواعد عربي</span>
                <span class="suggestion-chip" onclick="setSuggestedTitle('كيمياء - سرعة التفاعل والاتزان')">كيمياء</span>
            </div>
        </div>
        <div class="ai-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeRenameModal()">{{ __('إلغاء') }}</button>
            <button type="button" class="btn-modal-save" id="btnSaveRenameModal">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>{{ __('حفظ الاسم') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- نافذة تأكيد حذف المحادثة (Delete Confirmation Modal)                      -->
<!-- ========================================================================= -->
<div class="ai-modal-overlay" id="stepDeleteModalOverlay">
    <div class="ai-modal-card modal-card-danger">
        <div class="ai-modal-header">
            <div class="modal-header-title">
                <div class="modal-icon-badge badge-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3>{{ __('حذف المحادثة؟') }}</h3>
                    <p class="modal-subtitle">{{ __('سيتم حذف هذه المحادثة وكافة رسائلها بشكل نهائي.') }}</p>
                </div>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="ai-modal-body">
            <p style="margin: 0; color: var(--ai-text-secondary); font-size: 0.95rem; line-height: 1.6;">
                {{ __('هل أنت متأكد من رغبتك في حذف محادثة') }} <strong id="deleteTargetConvTitle" style="color: var(--ai-text-primary);"></strong>؟ {{ __('لا يمكن التراجع عن هذه الخطوة.') }}
            </p>
        </div>
        <div class="ai-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">{{ __('إلغاء') }}</button>
            <button type="button" class="btn-modal-danger-confirm" id="btnConfirmDeleteModal">
                <i class="fa-regular fa-trash-can"></i>
                <span>{{ __('تأكيد الحذف') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- نافذة الإعدادات الشاملة (Settings Modal)                                  -->
<!-- ========================================================================= -->
<div class="ai-modal-overlay" id="stepSettingsModalOverlay">
    <div class="ai-modal-card modal-card-settings">
        <div class="ai-modal-header">
            <div class="modal-header-title">
                <div class="modal-icon-badge">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <div>
                    <h3>{{ __('إعدادات Step by Step Ai') }}</h3>
                    <p class="modal-subtitle">{{ __('تخصيص تجربة الذكاء الاصطناعي والمظهر وخيارات المحادثة') }}</p>
                </div>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeSettingsModal()">&times;</button>
        </div>
        <div class="ai-modal-body settings-modal-body">
            <!-- تبويبات الإعدادات -->
            <div class="settings-tabs-row">
                <button type="button" class="settings-tab-btn active" data-tab="general">
                    <i class="fa-solid fa-palette"></i> {{ __('المظهر العام') }}
                </button>
                <button type="button" class="settings-tab-btn" data-tab="chat">
                    <i class="fa-solid fa-comment-dots"></i> {{ __('خيارات الدردشة') }}
                </button>
                <button type="button" class="settings-tab-btn" data-tab="ai">
                    <i class="fa-solid fa-brain"></i> {{ __('محرك الذكاء') }}
                </button>
                <button type="button" class="settings-tab-btn" data-tab="about">
                    <i class="fa-solid fa-circle-info"></i> {{ __('حول المساعد') }}
                </button>
            </div>

            <!-- محتوى تبويب المظهر العام -->
            <div class="settings-tab-content active" id="tabContent_general">
                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('وضع المظهر (Theme)') }}</span>
                        <span class="setting-desc">{{ __('اختر بين الوضع الفاتح أو الداكن أو التلقائي المتوافق مع نظام جهازك.') }}</span>
                    </div>
                    <div class="theme-select-pills">
                        <button type="button" class="theme-pill" data-theme="light">
                            <i class="fa-solid fa-sun"></i> {{ __('فاتح') }}
                        </button>
                        <button type="button" class="theme-pill" data-theme="dark">
                            <i class="fa-solid fa-moon"></i> {{ __('داكن') }}
                        </button>
                        <button type="button" class="theme-pill" data-theme="system">
                            <i class="fa-solid fa-desktop"></i> {{ __('تلقائي') }}
                        </button>
                    </div>
                </div>

                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('لغة الرموز الرياضية الافتراضية') }}</span>
                        <span class="setting-desc">{{ __('عرض الرموز بصيغة المنهاج العربي المعتمد أو بالرموز اللاتينية الإنجليزية.') }}</span>
                    </div>
                    <div class="theme-select-pills">
                        <button type="button" class="symbol-pill active" data-symbol="ar">عربي (ق=ك×ت)</button>
                        <button type="button" class="symbol-pill" data-symbol="en">English (F=ma)</button>
                    </div>
                </div>
            </div>

            <!-- محتوى تبويب خيارات الدردشة -->
            <div class="settings-tab-content" id="tabContent_chat">
                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('الإرسال بزر Enter') }}</span>
                        <span class="setting-desc">{{ __('إرسال الرسالة مباشرة عند الضغط على Enter، واستخدام Shift+Enter للسطر الجديد.') }}</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="settingEnterToSend" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('إظهار التوقيت الزمني للرسائل') }}</span>
                        <span class="setting-desc">{{ __('عرض وقت إرسال كل رسالة أسفل الفقاعة.') }}</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="settingShowTimestamps" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- محتوى تبويب محرك الذكاء -->
            <div class="settings-tab-content" id="tabContent_ai">
                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('النموذج الافتراضي') }}</span>
                        <span class="setting-desc">{{ __('اختر النموذج المفضل لبدء المحادثات الجديدة.') }}</span>
                    </div>
                    <select id="settingDefaultModel" class="settings-select">
                        <option value="smart">Step by Step Smart (Gemini 3.8 Flash)</option>
                        <option value="fast">Fast Lite (3.8 Flash Lite)</option>
                        <option value="balanced">Balanced Core (3.5 Core)</option>
                    </select>
                </div>

                <div class="setting-item-row">
                    <div class="setting-item-info">
                        <span class="setting-title">{{ __('نمط الإجابة الأكاديمي') }}</span>
                        <span class="setting-desc">{{ __('مستوى تفصيل الشروحات وحل المسائل.') }}</span>
                    </div>
                    <select id="settingResponseStyle" class="settings-select">
                        <option value="step-by-step">شرح مفصل خطوة بخطوة مع القوانين (موصى به)</option>
                        <option value="concise">موجز ومباشر للاختبارات السريعة</option>
                        <option value="deep">أكاديمي متعمق مع أمثلة إثرائية</option>
                    </select>
                </div>
            </div>

            <!-- محتوى تبويب حول المساعد -->
            <div class="settings-tab-content" id="tabContent_about">
                <div class="about-card-box">
                    <img src="{{ asset('logo.png') }}" alt="Step by Step" class="about-logo" onerror="this.src='{{ asset('images/logo.png') }}'">
                    <h4>Step by Step Ai — إصدار 4.0 Pro</h4>
                    <p>منظومة الذكاء الاصطناعي الأكاديمية الشاملة لمنصة <strong>Step by Step</strong> التعليمية الرقمية (ستيبفورو - stepvoro.com).</p>
                    <div class="about-badges-row">
                        <span class="about-badge"><i class="fa-solid fa-shield-halved"></i> حماية تامة للخصوصية</span>
                        <span class="about-badge"><i class="fa-solid fa-bolt"></i> Real-time Token Streaming</span>
                        <span class="about-badge"><i class="fa-solid fa-square-root-variable"></i> KaTeX Math LTR</span>
                        <span class="about-badge"><i class="fa-solid fa-code"></i> Code Highlighting</span>
                    </div>
                    <div class="about-footer-text">
                        بإشراف وتأسيس <strong>المهندس أحمد شمالي</strong> &bull; فلسطين الحبيبة
                    </div>
                </div>
            </div>
        </div>
        <div class="ai-modal-footer">
            <button type="button" class="btn-modal-save" onclick="closeSettingsModal()">
                <i class="fa-solid fa-check"></i>
                <span>{{ __('تم وحفظ الخيارات') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- حاوية التنبيهات العائمة (Toasts) -->
<div id="stepToastContainer" class="step-toast-container"></div>

<!-- ========================================================================= -->
<!-- تضمين الخطوط والمكتبات الخارجية (KaTeX, Marked)                          -->
<!-- ========================================================================= -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Fira+Code:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

<!-- KaTeX للرياضيات و Marked للـ Markdown -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<!-- ========================================================================= -->
<!-- تنسيقات CSS الفاخرة لمنظومة Step by Step Ai (Light & Dark Mode)          -->
<!-- ========================================================================= -->
<style>
:root {
    --ai-font-main: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --ai-font-en: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    --ai-font-mono: 'Fira Code', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

    /* ألوان المنصة المستوحاة من الشعار */
    --brand-navy: #004b93;
    --brand-blue: #1d4ed8;
    --brand-blue-hover: #1e40af;
    --brand-orange: #ea580c;
    --brand-amber: #f59e0b;
    --brand-emerald: #10b981;

    /* المتغيرات للوضع الفاتح (Light Mode) */
    --ai-bg-main: #f8fafc;
    --ai-bg-surface: #ffffff;
    --ai-bg-sidebar: #ffffff;
    --ai-border: #e2e8f0;
    --ai-border-subtle: #f1f5f9;
    --ai-text-primary: #0f172a;
    --ai-text-secondary: #475569;
    --ai-text-muted: #94a3b8;
    --ai-hover-bg: #f1f5f9;
    --ai-user-bubble-bg: linear-gradient(135deg, #004b93 0%, #1d4ed8 100%);
    --ai-user-bubble-text: #ffffff;
    --ai-assistant-bubble-bg: #ffffff;
    --ai-code-bg: #0f172a;
    --ai-code-text: #e2e8f0;
    --ai-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
    --ai-shadow-md: 0 4px 14px rgba(0, 0, 0, 0.07);
    --ai-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* المتغيرات للوضع الداكن (Dark Mode) */
html.dark-theme :root,
body.dark-theme :root,
.step-ai-container.dark-theme {
    --ai-bg-main: #0b0f19;
    --ai-bg-surface: #111827;
    --ai-bg-sidebar: #0f172a;
    --ai-border: #1e293b;
    --ai-border-subtle: #172033;
    --ai-text-primary: #f8fafc;
    --ai-text-secondary: #cbd5e1;
    --ai-text-muted: #64748b;
    --ai-hover-bg: #1e293b;
    --ai-user-bubble-bg: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
    --ai-user-bubble-text: #ffffff;
    --ai-assistant-bubble-bg: #111827;
    --ai-code-bg: #090d16;
    --ai-code-text: #e2e8f0;
    --ai-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --ai-shadow-md: 0 4px 14px rgba(0, 0, 0, 0.4);
    --ai-shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.5);
}

/* الهيكل والحاوية الرئيسية */
.step-ai-container {
    display: flex;
    height: calc(100vh - 65px);
    width: 100%;
    background: var(--ai-bg-main);
    font-family: var(--ai-font-main);
    color: var(--ai-text-primary);
    overflow: hidden;
    position: relative;
    transition: background-color 0.25s ease, color 0.25s ease;
}

/* ========================================================================= */
/* 1. الشريط الجانبي (Sidebar)                                              */
/* ========================================================================= */
.step-ai-sidebar {
    width: 290px;
    background: var(--ai-bg-sidebar);
    border-inline-end: 1px solid var(--ai-border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: width 0.28s cubic-bezier(0.16, 1, 0.3, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 50;
    position: relative;
}

.step-ai-container.sidebar-collapsed .step-ai-sidebar {
    width: 0;
    overflow: hidden;
    border-inline-end: none;
}

.sidebar-brand-box {
    padding: 16px 18px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--ai-border);
}

.brand-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
}

.brand-logo-img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0, 75, 147, 0.2);
}

.brand-titles {
    display: flex;
    flex-direction: column;
}

.brand-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--brand-navy);
    line-height: 1.2;
}

.dark-theme .brand-name {
    color: #60a5fa;
}

.brand-sub {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--brand-orange);
}

.btn-sidebar-close-mobile {
    display: none;
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--ai-text-muted);
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
}

.sidebar-action-header {
    padding: 14px 16px 8px;
}

.btn-new-chat {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-blue) 100%);
    color: #ffffff;
    border: none;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(29, 78, 216, 0.25);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-new-chat:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(29, 78, 216, 0.35);
}

.shortcut-tag {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 7px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-family: var(--ai-font-en);
    font-weight: 700;
}

.sidebar-search-box {
    margin: 6px 16px 10px;
    position: relative;
    display: flex;
    align-items: center;
}

.sidebar-search-box .search-icon {
    position: absolute;
    inset-inline-start: 12px;
    color: var(--ai-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

.conv-search-input {
    width: 100%;
    padding: 8px 34px 8px 34px;
    border-radius: 10px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-main);
    color: var(--ai-text-primary);
    font-size: 0.82rem;
    outline: none;
    transition: all 0.18s ease;
}

.conv-search-input:focus {
    border-color: var(--brand-blue);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.btn-search-clear {
    position: absolute;
    inset-inline-end: 10px;
    background: none;
    border: none;
    color: var(--ai-text-muted);
    font-size: 0.8rem;
    cursor: pointer;
}

.sidebar-conversations-section {
    flex: 1;
    overflow-y: auto;
    padding: 0 10px 10px;
    display: flex;
    flex-direction: column;
}

.section-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 8px 6px;
    font-size: 0.74rem;
    font-weight: 800;
    color: var(--ai-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.conv-count-badge {
    background: var(--ai-hover-bg);
    color: var(--ai-text-secondary);
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
}

.conversations-scroll-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-group-heading {
    font-size: 0.72rem;
    font-weight: 800;
    color: var(--ai-text-muted);
    padding: 10px 8px 4px;
    letter-spacing: 0.3px;
}

.conversation-nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.18s ease;
    position: relative;
    color: var(--ai-text-secondary);
}

.conversation-nav-item:hover {
    background: var(--ai-hover-bg);
    color: var(--ai-text-primary);
}

.conversation-nav-item.active-conv {
    background: #eff6ff;
    color: var(--brand-blue);
    font-weight: 700;
}

.dark-theme .conversation-nav-item.active-conv {
    background: rgba(37, 99, 235, 0.15);
    color: #93c5fd;
}

.conversation-main-click {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}

.conv-msg-icon {
    font-size: 0.95rem;
    flex-shrink: 0;
    color: inherit;
}

.conv-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
}

.conv-title-text {
    font-size: 0.84rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
}

.conv-meta-date {
    font-size: 0.68rem;
    color: var(--ai-text-muted);
}

.conv-actions-group {
    display: none;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.conversation-nav-item:hover .conv-actions-group,
.conversation-nav-item.active-conv .conv-actions-group {
    display: flex;
}

.btn-conv-action {
    background: none;
    border: none;
    color: var(--ai-text-muted);
    padding: 4px 6px;
    border-radius: 6px;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-conv-action:hover {
    color: var(--ai-text-primary);
    background: rgba(0, 0, 0, 0.05);
}

.btn-conv-action.btn-delete-conv:hover {
    color: #ef4444;
}

.empty-conversations-state {
    text-align: center;
    padding: 30px 14px;
    color: var(--ai-text-muted);
}

.empty-icon-wrap {
    font-size: 2rem;
    margin-bottom: 8px;
    color: var(--brand-blue);
    opacity: 0.6;
}

.sidebar-footer {
    padding: 12px 14px;
    border-top: 1px solid var(--ai-border);
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: var(--ai-bg-sidebar);
}

.user-profile-strip {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-blue) 100%);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 0.95rem;
    flex-shrink: 0;
    overflow: hidden;
}

.user-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-strip-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
}

.user-strip-name {
    font-size: 0.85rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-strip-role {
    font-size: 0.7rem;
    color: var(--ai-text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
}

.text-amber { color: #f59e0b; }
.text-blue { color: #3b82f6; }
.text-purple { color: #a855f7; }
.text-emerald { color: #10b981; }
.text-cyan { color: #06b6d4; }

.footer-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.btn-footer-tool {
    flex: 1;
    display: grid;
    place-items: center;
    height: 34px;
    border-radius: 8px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-main);
    color: var(--ai-text-secondary);
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.16s ease;
}

.btn-footer-tool:hover {
    background: var(--ai-hover-bg);
    color: var(--ai-text-primary);
}

.btn-footer-tool.btn-danger-action:hover {
    color: #ef4444;
    border-color: #fca5a5;
}

/* ========================================================================= */
/* 2. منطقة الدردشة الرئيسية (Main Chat Workspace)                          */
/* ========================================================================= */
.step-ai-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--ai-bg-main);
    position: relative;
    overflow: hidden;
}

/* الترويسة العلوية */
.chat-top-header {
    height: 64px;
    background: var(--ai-bg-surface);
    border-bottom: 1px solid var(--ai-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    flex-shrink: 0;
    z-index: 20;
}

.header-left-group {
    display: flex;
    align-items: center;
    gap: 14px;
}

.btn-toggle-sidebar {
    background: none;
    border: 1px solid var(--ai-border);
    width: 36px;
    height: 36px;
    border-radius: 9px;
    color: var(--ai-text-secondary);
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: 0.95rem;
    transition: all 0.16s ease;
}

.btn-toggle-sidebar:hover {
    background: var(--ai-hover-bg);
    color: var(--ai-text-primary);
}

.brand-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.brand-text-column {
    display: flex;
    flex-direction: column;
}

.brand-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.brand-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 850;
    color: var(--ai-text-primary);
    line-height: 1.2;
}

.brand-tagline {
    font-size: 0.7rem;
    color: var(--ai-text-muted);
}

/* قائمة اختيار النموذج */
.model-selector-dropdown {
    position: relative;
}

.model-badge-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: var(--brand-blue);
    border: 1px solid #bfdbfe;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.16s ease;
}

.dark-theme .model-badge-btn {
    background: rgba(37, 99, 235, 0.2);
    border-color: rgba(59, 130, 246, 0.4);
    color: #93c5fd;
}

.model-badge-btn:hover {
    background: #dbeafe;
}

.model-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    inset-inline-start: 0;
    width: 260px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    border-radius: 12px;
    box-shadow: var(--ai-shadow-lg);
    padding: 6px;
    z-index: 100;
    flex-direction: column;
    gap: 4px;
}

.model-dropdown-menu.active {
    display: flex;
}

.model-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s ease;
}

.model-option:hover {
    background: var(--ai-hover-bg);
}

.model-option.active {
    background: #eff6ff;
    color: var(--brand-blue);
}

.dark-theme .model-option.active {
    background: rgba(37, 99, 235, 0.2);
    color: #93c5fd;
}

.model-option-info {
    display: flex;
    flex-direction: column;
}

.model-option .model-name {
    font-size: 0.82rem;
    font-weight: 750;
}

.model-option .model-desc {
    font-size: 0.68rem;
    color: var(--ai-text-muted);
}

.model-option .check-icon {
    display: none;
    font-size: 0.8rem;
}

.model-option.active .check-icon {
    display: block;
}

/* الجانب الأيمن للترويسة */
.header-right-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.active-conv-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--ai-bg-main);
    border: 1px solid var(--ai-border);
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    max-width: 250px;
}

.active-conv-icon {
    color: var(--brand-blue);
}

.active-conv-title {
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
}

.btn-pill-action {
    background: none;
    border: none;
    color: var(--ai-text-muted);
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    font-size: 0.78rem;
}

.btn-pill-action:hover {
    color: var(--ai-text-primary);
}

.btn-pill-action.btn-pill-danger:hover {
    color: #ef4444;
}

.btn-symbol-mode-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 750;
    color: var(--ai-text-secondary);
    cursor: pointer;
    transition: all 0.16s ease;
}

.btn-symbol-mode-toggle:hover {
    background: var(--ai-hover-bg);
    color: var(--brand-blue);
    border-color: #93c5fd;
}

.btn-header-tool {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--ai-text-secondary);
    cursor: pointer;
    transition: all 0.16s ease;
}

.btn-header-tool:hover {
    background: var(--ai-hover-bg);
    color: var(--brand-navy);
}

/* ========================================================================= */
/* منطقة الرسائل وتدفق المحادثة                                            */
/* ========================================================================= */
.chat-messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 24px 20px 40px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    scroll-behavior: smooth;
}

/* شاشة الترحيب */
.chat-welcome-hero {
    max-width: 760px;
    margin: auto auto 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    padding: 20px 10px;
}

.welcome-logo-badge {
    width: 82px;
    height: 82px;
    border-radius: 50%;
    padding: 4px;
    background: var(--ai-bg-surface);
    box-shadow: 0 6px 24px rgba(0, 75, 147, 0.18);
    display: grid;
    place-items: center;
}

.welcome-brand-logo {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.welcome-heading {
    font-size: 1.55rem;
    font-weight: 850;
    margin: 0;
    color: var(--ai-text-primary);
    line-height: 1.35;
}

.welcome-subheading {
    font-size: 0.88rem;
    color: var(--ai-text-secondary);
    max-width: 620px;
    margin: 0;
    line-height: 1.65;
}

/* شريط الأدوات التعليمية السريعة */
.edu-tools-strip {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 6px 0 12px;
}

.edu-tool-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 999px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    color: var(--ai-text-secondary);
    font-size: 0.78rem;
    font-weight: 750;
    cursor: pointer;
    box-shadow: var(--ai-shadow-sm);
    transition: all 0.18s ease;
}

.edu-tool-pill:hover {
    background: #eff6ff;
    color: var(--brand-blue);
    border-color: #bfdbfe;
    transform: translateY(-1px);
}

.dark-theme .edu-tool-pill:hover {
    background: rgba(37, 99, 235, 0.2);
    border-color: #3b82f6;
    color: #93c5fd;
}

/* شبكة البطاقات المقترحة */
.quick-prompts-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    width: 100%;
    text-align: start;
}

.prompt-card {
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    box-shadow: var(--ai-shadow-sm);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.prompt-card:hover {
    transform: translateY(-2px);
    border-color: #93c5fd;
    box-shadow: var(--ai-shadow-md);
}

.prompt-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.icon-blue { background: #eff6ff; color: #1d4ed8; }
.icon-emerald { background: #ecfdf5; color: #059669; }
.icon-amber { background: #fffbeb; color: #d97706; }
.icon-purple { background: #faf5ff; color: #7c3aed; }

.prompt-card-text {
    flex: 1;
    min-width: 0;
}

.prompt-card-text h4 {
    margin: 0 0 3px;
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--ai-text-primary);
}

.prompt-card-text p {
    margin: 0;
    font-size: 0.74rem;
    color: var(--ai-text-muted);
    line-height: 1.45;
}

.prompt-card-arrow {
    font-size: 0.8rem;
    color: var(--ai-text-muted);
    transition: transform 0.2s ease;
}

.prompt-card:hover .prompt-card-arrow {
    color: var(--brand-blue);
    transform: translateX(-4px);
}

[dir="ltr"] .prompt-card:hover .prompt-card-arrow {
    transform: translateX(4px);
}

/* صفوف الرسائل (Chat Message Rows) */
.chat-msg-row {
    max-width: 820px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    gap: 14px;
    animation: msgFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes msgFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-msg-user {
    flex-direction: row-reverse;
}

.msg-avatar {
    width: 36px;
    height: 36px;
    border-radius: 11px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    font-size: 0.95rem;
}

.chat-msg-user .msg-avatar {
    background: #e2e8f0;
    color: #1e293b;
}

.dark-theme .chat-msg-user .msg-avatar {
    background: #334155;
    color: #f8fafc;
}

.chat-msg-assistant .msg-avatar {
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 75, 147, 0.15);
    overflow: hidden;
}

.msg-avatar-ai-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.msg-body-wrapper {
    display: flex;
    flex-direction: column;
    max-width: 85%;
    min-width: 0;
}

.chat-msg-assistant .msg-body-wrapper {
    max-width: 100%;
    width: 100%;
}

.msg-author-time {
    font-size: 0.72rem;
    font-weight: 750;
    color: var(--ai-text-muted);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.chat-msg-user .msg-author-time {
    justify-content: flex-end;
}

/* فقاعة رسالة المستخدم */
.chat-msg-user .msg-bubble {
    background: var(--ai-user-bubble-bg);
    color: var(--ai-user-bubble-text);
    border-radius: 18px 18px 4px 18px;
    padding: 12px 18px;
    font-size: 0.94rem;
    line-height: 1.65;
    box-shadow: 0 3px 12px rgba(0, 75, 147, 0.2);
    word-break: break-word;
}

[dir="ltr"] .chat-msg-user .msg-bubble {
    border-radius: 18px 18px 18px 4px;
}

.chat-msg-user .msg-bubble img {
    max-width: 280px;
    max-height: 220px;
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    margin-bottom: 8px;
    display: block;
}

.user-file-attachment-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.15);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    margin-bottom: 8px;
}

/* فقاعة رد الذكاء الاصطناعي (Step by Step Ai) */
.chat-msg-assistant .msg-bubble {
    background: var(--ai-assistant-bubble-bg);
    border: 1px solid var(--ai-border);
    border-radius: 18px;
    padding: 18px 22px;
    color: var(--ai-text-primary);
    font-size: 0.96rem;
    line-height: 1.85;
    box-shadow: var(--ai-shadow-sm);
    word-break: break-word;
}

/* تنسيقات Markdown داخل ردود الذكاء */
.chat-msg-assistant .msg-bubble h1,
.chat-msg-assistant .msg-bubble h2,
.chat-msg-assistant .msg-bubble h3,
.chat-msg-assistant .msg-bubble h4 {
    color: var(--ai-text-primary);
    margin: 18px 0 8px;
    font-weight: 850;
    line-height: 1.35;
}

.chat-msg-assistant .msg-bubble h1 { font-size: 1.3rem; border-bottom: 1px solid var(--ai-border); padding-bottom: 6px; }
.chat-msg-assistant .msg-bubble h2 { font-size: 1.15rem; }
.chat-msg-assistant .msg-bubble h3 { font-size: 1.05rem; }
.chat-msg-assistant .msg-bubble h4 { font-size: 0.95rem; }

.chat-msg-assistant .msg-bubble p {
    margin: 0 0 12px;
}

.chat-msg-assistant .msg-bubble p:last-child {
    margin-bottom: 0;
}

.chat-msg-assistant .msg-bubble ul,
.chat-msg-assistant .msg-bubble ol {
    margin: 8px 0 14px;
    padding-inline-start: 24px;
}

.chat-msg-assistant .msg-bubble li {
    margin-bottom: 6px;
}

.chat-msg-assistant .msg-bubble strong {
    font-weight: 800;
    color: var(--brand-navy);
}

.dark-theme .chat-msg-assistant .msg-bubble strong {
    color: #93c5fd;
}

.chat-msg-assistant .msg-bubble blockquote {
    margin: 12px 0;
    padding: 10px 16px;
    border-inline-start: 4px solid var(--brand-blue);
    background: var(--ai-hover-bg);
    border-radius: 0 8px 8px 0;
    color: var(--ai-text-secondary);
}

/* الجداول */
.chat-msg-assistant .msg-bubble table {
    width: 100%;
    border-collapse: collapse;
    margin: 14px 0;
    font-size: 0.88rem;
    overflow-x: auto;
    display: block;
}

.chat-msg-assistant .msg-bubble th,
.chat-msg-assistant .msg-bubble td {
    padding: 10px 14px;
    border: 1px solid var(--ai-border);
    text-align: start;
}

.chat-msg-assistant .msg-bubble th {
    background: var(--ai-hover-bg);
    font-weight: 800;
}

/* تنسيقات كود البرمجة (Code Blocks) */
.code-block-wrapper {
    margin: 14px 0;
    border-radius: 12px;
    overflow: hidden;
    background: var(--ai-code-bg);
    border: 1px solid #1e293b;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    font-family: var(--ai-font-mono);
}

.code-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #1e293b;
    padding: 6px 14px;
    font-size: 0.74rem;
    color: #94a3b8;
    font-weight: 600;
}

.code-lang-label {
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-copy-code {
    background: none;
    border: none;
    color: #cbd5e1;
    font-size: 0.74rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.15s ease;
}

.btn-copy-code:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.1);
}

.code-block-wrapper pre {
    margin: 0;
    padding: 14px 16px;
    overflow-x: auto;
    direction: ltr;
    text-align: left;
}

.code-block-wrapper code {
    font-family: var(--ai-font-mono);
    font-size: 0.88rem;
    color: var(--ai-code-text);
    line-height: 1.6;
}

/* كود مضمن بداخل السطر */
.chat-msg-assistant .msg-bubble :not(pre) > code {
    background: var(--ai-hover-bg);
    color: #e11d48;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 0.86rem;
    font-family: var(--ai-font-mono);
    border: 1px solid var(--ai-border);
}

/* معادلات KaTeX */
.katex-display {
    margin: 12px 0 !important;
    direction: ltr !important;
    text-align: center !important;
}

/* أدوات التفاعل أسفل رد الذكاء (Action Bar) */
.msg-actions-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 0 4px;
}

.btn-msg-action {
    background: none;
    border: none;
    color: var(--ai-text-muted);
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 6px;
    transition: all 0.15s ease;
}

.btn-msg-action:hover {
    color: var(--ai-text-primary);
    background: var(--ai-hover-bg);
}

.btn-msg-action.active-liked {
    color: var(--brand-emerald);
}

.btn-msg-action.active-disliked {
    color: #ef4444;
}

/* مؤشر التفكير / الكتابة */
.chat-typing-row {
    max-width: 820px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    gap: 14px;
    align-items: center;
}

.ai-avatar-tiny {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: var(--ai-shadow-sm);
    display: grid;
    place-items: center;
    overflow: hidden;
}

.ai-avatar-tiny img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.typing-bubble {
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    padding: 10px 16px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.typing-text {
    font-size: 0.82rem;
    color: var(--ai-text-secondary);
    font-weight: 600;
}

.typing-wave-dots {
    display: flex;
    gap: 4px;
}

.typing-wave-dots span {
    width: 6px;
    height: 6px;
    background: var(--brand-blue);
    border-radius: 50%;
    animation: wavePulse 1.4s infinite ease-in-out both;
}

.typing-wave-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-wave-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes wavePulse {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

.typing-cursor {
    display: inline-block;
    color: var(--brand-blue);
    animation: blinkCursor 0.8s infinite;
}

@keyframes blinkCursor {
    50% { opacity: 0; }
}

/* زر إيقاف التوليد (Stop Generating) */
.stop-generating-dock {
    position: absolute;
    bottom: 95px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 30;
}

.btn-stop-generating {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 999px;
    background: var(--ai-bg-surface);
    border: 1px solid #f87171;
    color: #ef4444;
    font-size: 0.82rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.18);
    transition: all 0.18s ease;
}

.btn-stop-generating:hover {
    background: #fef2f2;
    transform: translateY(-1px);
}

/* ========================================================================= */
/* 3. شريط المعاينة والإدخال (Chat Input Dock)                              */
/* ========================================================================= */
.attached-preview-bar {
    padding: 8px 20px 0;
    max-width: 820px;
    width: 100%;
    margin: 0 auto;
}

.attached-file-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    padding: 6px 12px;
    border-radius: 12px;
    box-shadow: var(--ai-shadow-sm);
}

.attached-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #eff6ff;
    color: var(--brand-blue);
    display: grid;
    place-items: center;
    overflow: hidden;
    font-size: 0.95rem;
}

.attached-icon-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.attached-details {
    display: flex;
    flex-direction: column;
}

.attached-name {
    font-size: 0.8rem;
    font-weight: 750;
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attached-size {
    font-size: 0.68rem;
    color: var(--ai-text-muted);
}

.btn-remove-attachment {
    background: none;
    border: none;
    color: var(--ai-text-muted);
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
}

.btn-remove-attachment:hover {
    color: #ef4444;
}

.chat-input-dock {
    padding: 10px 20px 18px;
    background: transparent;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.chat-input-form {
    max-width: 820px;
    width: 100%;
    background: var(--ai-bg-surface);
    border: 1.5px solid var(--ai-border);
    border-radius: 24px;
    padding: 8px 12px;
    display: flex;
    align-items: flex-end;
    gap: 8px;
    box-shadow: var(--ai-shadow-md);
    transition: all 0.2s ease;
}

.chat-input-form:focus-within {
    border-color: var(--brand-blue);
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.16);
}

.dock-actions-start,
.dock-actions-end {
    display: flex;
    align-items: center;
    gap: 4px;
    padding-bottom: 2px;
}

.dock-tool-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: none;
    border: none;
    color: var(--ai-text-muted);
    font-size: 1rem;
    cursor: pointer;
    display: grid;
    place-items: center;
    transition: all 0.16s ease;
}

.dock-tool-btn:hover {
    background: var(--ai-hover-bg);
    color: var(--ai-text-primary);
}

.dock-tool-btn.active-listening {
    color: #ef4444;
    background: #fef2f2;
    animation: micPulse 1s infinite alternate;
}

@keyframes micPulse {
    from { transform: scale(1); }
    to { transform: scale(1.1); }
}

.dock-textarea {
    flex: 1;
    border: none;
    background: none;
    color: var(--ai-text-primary);
    font-family: var(--ai-font-main);
    font-size: 0.94rem;
    line-height: 1.6;
    padding: 6px 4px;
    resize: none;
    outline: none;
    max-height: 200px;
    min-height: 24px;
}

.dock-textarea::placeholder {
    color: var(--ai-text-muted);
    font-size: 0.88rem;
}

.btn-dock-send {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--brand-navy);
    color: #ffffff;
    border: none;
    display: grid;
    place-items: center;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.18s ease;
}

.btn-dock-send:disabled {
    background: var(--ai-border);
    color: var(--ai-text-muted);
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-dock-send:not(:disabled):hover {
    background: var(--brand-blue);
    transform: scale(1.05);
}

.dock-bottom-note {
    font-size: 0.7rem;
    color: var(--ai-text-muted);
    margin-top: 6px;
    text-align: center;
}

/* ========================================================================= */
/* النوافذ المنبثقة (Modals)                                                */
/* ========================================================================= */
.ai-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.ai-modal-overlay.active {
    display: flex;
}

.ai-modal-card {
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    border-radius: 20px;
    max-width: 480px;
    width: 100%;
    box-shadow: var(--ai-shadow-lg);
    overflow: hidden;
    animation: modalPop 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.ai-modal-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--ai-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-icon-badge {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: #eff6ff;
    color: var(--brand-blue);
    display: grid;
    place-items: center;
    font-size: 1.1rem;
}

.modal-icon-badge.badge-danger {
    background: #fef2f2;
    color: #ef4444;
}

.modal-header-title h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 850;
    color: var(--ai-text-primary);
}

.modal-subtitle {
    margin: 2px 0 0;
    font-size: 0.74rem;
    color: var(--ai-text-muted);
}

.btn-modal-close {
    background: none;
    border: none;
    font-size: 1.4rem;
    color: var(--ai-text-muted);
    cursor: pointer;
}

.ai-modal-body {
    padding: 20px 22px;
}

.modal-input-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 750;
    margin-bottom: 6px;
    color: var(--ai-text-primary);
}

.modal-text-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-main);
    color: var(--ai-text-primary);
    font-size: 0.9rem;
    outline: none;
    box-sizing: border-box;
}

.modal-text-input:focus {
    border-color: var(--brand-blue);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.modal-quick-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.suggestion-chip {
    background: var(--ai-hover-bg);
    border: 1px solid var(--ai-border);
    color: var(--ai-text-secondary);
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.suggestion-chip:hover {
    background: #eff6ff;
    color: var(--brand-blue);
}

.ai-modal-footer {
    padding: 14px 22px;
    border-top: 1px solid var(--ai-border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    background: var(--ai-bg-main);
}

.btn-modal-cancel {
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-surface);
    color: var(--ai-text-secondary);
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
}

.btn-modal-save {
    padding: 8px 18px;
    border-radius: 10px;
    border: none;
    background: var(--brand-blue);
    color: #ffffff;
    font-size: 0.84rem;
    font-weight: 750;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-modal-danger-confirm {
    padding: 8px 18px;
    border-radius: 10px;
    border: none;
    background: #ef4444;
    color: #ffffff;
    font-size: 0.84rem;
    font-weight: 750;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* نافذة الإعدادات */
.modal-card-settings {
    max-width: 600px;
}

.settings-tabs-row {
    display: flex;
    gap: 6px;
    border-bottom: 1px solid var(--ai-border);
    padding-bottom: 10px;
    margin-bottom: 16px;
    overflow-x: auto;
}

.settings-tab-btn {
    padding: 6px 14px;
    border-radius: 8px;
    border: none;
    background: none;
    color: var(--ai-text-secondary);
    font-size: 0.82rem;
    font-weight: 750;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.settings-tab-btn.active {
    background: #eff6ff;
    color: var(--brand-blue);
}

.dark-theme .settings-tab-btn.active {
    background: rgba(37, 99, 235, 0.2);
    color: #93c5fd;
}

.settings-tab-content {
    display: none;
    flex-direction: column;
    gap: 16px;
}

.settings-tab-content.active {
    display: flex;
}

.setting-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--ai-border-subtle);
}

.setting-item-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.setting-title {
    font-size: 0.88rem;
    font-weight: 750;
    color: var(--ai-text-primary);
}

.setting-desc {
    font-size: 0.74rem;
    color: var(--ai-text-muted);
    margin-top: 2px;
}

.theme-select-pills {
    display: flex;
    gap: 6px;
}

.theme-pill, .symbol-pill {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-main);
    color: var(--ai-text-secondary);
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
}

.theme-pill.active, .symbol-pill.active {
    background: var(--brand-blue);
    color: #ffffff;
    border-color: var(--brand-blue);
}

.settings-select {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid var(--ai-border);
    background: var(--ai-bg-main);
    color: var(--ai-text-primary);
    font-size: 0.82rem;
    outline: none;
}

/* مفتاح التبديل (Toggle Switch) */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: var(--ai-border);
    transition: 0.25s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.25s;
    border-radius: 50%;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: var(--brand-blue);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(20px);
}

/* بطاقة حول المساعد */
.about-card-box {
    text-align: center;
    padding: 10px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.about-logo {
    width: 64px;
    height: 64px;
    border-radius: 50%;
}

.about-card-box h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 850;
}

.about-card-box p {
    margin: 0;
    font-size: 0.82rem;
    color: var(--ai-text-secondary);
    max-width: 440px;
}

.about-badges-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    margin: 6px 0;
}

.about-badge {
    background: var(--ai-hover-bg);
    border: 1px solid var(--ai-border);
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    color: var(--ai-text-secondary);
}

.about-footer-text {
    font-size: 0.76rem;
    color: var(--brand-navy);
    font-weight: 700;
}

/* تنبيهات Toast */
.step-toast-container {
    position: fixed;
    bottom: 24px;
    inset-inline-end: 24px;
    z-index: 2000;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.step-toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 12px;
    background: var(--ai-bg-surface);
    border: 1px solid var(--ai-border);
    box-shadow: var(--ai-shadow-lg);
    font-size: 0.86rem;
    font-weight: 700;
    animation: toastSlide 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.step-toast.success { border-inline-start: 4px solid var(--brand-emerald); color: var(--ai-text-primary); }
.step-toast.error { border-inline-start: 4px solid #ef4444; color: var(--ai-text-primary); }

@keyframes toastSlide {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ========================================================================= */
/* التجاوب مع الشاشات (Responsive)                                         */
/* ========================================================================= */
@media (max-width: 900px) {
    .step-ai-sidebar {
        position: fixed;
        inset-block: 0;
        inset-inline-start: 0;
        height: 100vh;
        z-index: 1000;
        transform: translateX(-100%);
        box-shadow: var(--ai-shadow-lg);
    }
    [dir="rtl"] .step-ai-sidebar {
        transform: translateX(100%);
    }
    body.sidebar-mobile-open .step-ai-sidebar {
        transform: translateX(0);
    }
    body.sidebar-mobile-open .sidebar-mobile-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        z-index: 999;
    }
    .btn-sidebar-close-mobile {
        display: block;
    }
    .quick-prompts-grid {
        grid-template-columns: 1fr;
    }
    .active-conv-pill {
        display: none;
    }
}

@media (max-width: 600px) {
    .chat-top-header {
        padding: 0 12px;
    }
    .brand-tagline {
        display: none;
    }
    .tool-label-text {
        display: none;
    }
    .chat-messages-container {
        padding: 16px 10px 30px;
    }
    .chat-msg-assistant .msg-bubble {
        padding: 14px 16px;
        font-size: 0.92rem;
    }
    .chat-msg-user .msg-bubble {
        padding: 10px 14px;
        font-size: 0.9rem;
    }
    .chat-input-dock {
        padding: 6px 10px 14px;
    }
    .chat-input-form {
        padding: 6px 8px;
        border-radius: 20px;
    }
}
</style>

<!-- ========================================================================= -->
<!-- منطق JavaScript المتطور لمنظومة Step by Step Ai                           -->
<!-- ========================================================================= -->
<script>
(function() {
    'use strict';

    // 1. حالة النظام والمحادثة الحالية
    let currentConversationId = null;
    let currentSymbolMode = 'ar';
    let currentModel = 'smart';
    let attachedFilePayload = null; // { base64, mime, name, size, type: 'image'|'doc' }
    let isGenerating = false;
    let activeAbortController = null;
    let speechRecognitionInstance = null;
    const actorRole = @json($actor['role'] ?? 'student');

    // 2. حل المسار ديناميكياً ليعمل في الاستضافة المحلية والإنتاج
    function resolveApiUrl(path) {
        const cleanPath = path.startsWith('/') ? path : '/' + path;
        const subfolderMatch = window.location.pathname.match(/^(\/[^\/]+\/public|\/public)?/);
        const prefix = (subfolderMatch && subfolderMatch[0]) ? subfolderMatch[0] : '';
        return prefix + cleanPath;
    }

    // 3. عناصر DOM الرئيسية
    const container = document.getElementById('stepAiContainer');
    const sidebar = document.getElementById('stepAiSidebar');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const mobileSidebarCloseBtn = document.getElementById('mobileSidebarCloseBtn');
    const mobileBackdrop = document.getElementById('sidebarMobileBackdrop');
    const newChatBtn = document.getElementById('stepNewChatBtn');
    const convSearchInput = document.getElementById('convSearchInput');
    const convSearchClearBtn = document.getElementById('convSearchClearBtn');
    const conversationsList = document.getElementById('stepConversationsList');
    const convCountBadge = document.getElementById('stepConvCountBadge');
    const messagesBox = document.getElementById('stepMessagesBox');
    const welcomeBanner = document.getElementById('stepWelcomeBanner');
    const typingIndicator = document.getElementById('stepTypingIndicator');
    const typingTextNotice = document.getElementById('typingTextNotice');
    const stopGeneratingDock = document.getElementById('stopGeneratingDock');
    const btnStopGenerating = document.getElementById('btnStopGenerating');
    const attachmentBar = document.getElementById('stepAttachmentBar');
    const attachedIcon = document.getElementById('attachedIcon');
    const attachedThumbImg = document.getElementById('attachedThumbImg');
    const attachedFileName = document.getElementById('attachedFileName');
    const attachedFileSize = document.getElementById('attachedFileSize');
    const chatForm = document.getElementById('stepChatForm');
    const promptInput = document.getElementById('stepPromptInput');
    const submitBtn = document.getElementById('stepSubmitBtn');
    const uploadBtn = document.getElementById('stepUploadBtn');
    const fileInput = document.getElementById('stepFileInput');
    const voiceBtn = document.getElementById('stepVoiceBtn');
    const symbolToggleBtn = document.getElementById('stepSymbolToggleBtn');
    const symbolText = document.getElementById('stepSymbolText');
    const modelSelectorBtn = document.getElementById('modelSelectorBtn');
    const modelDropdownMenu = document.getElementById('modelDropdownMenu');
    const activeModelLabel = document.getElementById('activeModelLabel');
    const exportPdfBtn = document.getElementById('stepExportPdfBtn');
    const themeToggleBtn = document.getElementById('stepThemeToggleBtn');
    const settingsBtn = document.getElementById('stepSettingsBtn');
    const clearAllBtn = document.getElementById('stepClearAllBtn');

    // 4. إدارة المظهر (Light / Dark / System Theme)
    function applyTheme(theme) {
        if (theme === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            container.classList.toggle('dark-theme', prefersDark);
            document.body.classList.toggle('dark-theme', prefersDark);
        } else if (theme === 'dark') {
            container.classList.add('dark-theme');
            document.body.classList.add('dark-theme');
        } else {
            container.classList.remove('dark-theme');
            document.body.classList.remove('dark-theme');
        }
        localStorage.setItem('step_ai_theme', theme);

        // تحديث أزرار الإعدادات
        document.querySelectorAll('.theme-pill').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === theme);
        });

        // تحديث أيقونة الشريط
        const isDark = container.classList.contains('dark-theme');
        if (themeToggleBtn) {
            themeToggleBtn.querySelector('.theme-icon').className = isDark ? 'fa-solid fa-sun theme-icon' : 'fa-solid fa-moon theme-icon';
        }
    }

    const savedTheme = localStorage.getItem('step_ai_theme') || 'light';
    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isDark = container.classList.contains('dark-theme');
            applyTheme(isDark ? 'light' : 'dark');
        });
    }

    // 5. فتح وإغلاق الشريط الجانبي (Desktop & Mobile Drawer)
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                document.body.classList.toggle('sidebar-mobile-open');
            } else {
                container.classList.toggle('sidebar-collapsed');
            }
        });
    }

    if (mobileSidebarCloseBtn) {
        mobileSidebarCloseBtn.addEventListener('click', () => {
            document.body.classList.remove('sidebar-mobile-open');
        });
    }

    if (mobileBackdrop) {
        mobileBackdrop.addEventListener('click', () => {
            document.body.classList.remove('sidebar-mobile-open');
        });
    }

    // اختصار Ctrl+K أو Cmd+K لبدء محادثة جديدة
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            newChatBtn.click();
        }
    });

    // 6. اختيار النموذج (Model Selector)
    if (modelSelectorBtn && modelDropdownMenu) {
        modelSelectorBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            modelDropdownMenu.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            modelDropdownMenu.classList.remove('active');
        });

        document.querySelectorAll('.model-option').forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const selectedModel = opt.dataset.model;
                currentModel = selectedModel;

                document.querySelectorAll('.model-option').forEach(el => el.classList.remove('active'));
                opt.classList.add('active');

                if (selectedModel === 'smart') {
                    activeModelLabel.textContent = 'Smart 3.8 Flash';
                } else if (selectedModel === 'fast') {
                    activeModelLabel.textContent = 'Fast Lite';
                } else {
                    activeModelLabel.textContent = 'Balanced Core';
                }

                modelDropdownMenu.classList.remove('active');
                showToast(`تم التبديل إلى نموذج ${activeModelLabel.textContent} ⚡`, 'success');
            });
        });
    }

    // 7. تبديل وضع الرموز الرياضية (عربي / إنجليزي)
    if (symbolToggleBtn) {
        symbolToggleBtn.addEventListener('click', () => {
            setSymbolMode(currentSymbolMode === 'ar' ? 'en' : 'ar');
        });
    }

    function setSymbolMode(mode) {
        currentSymbolMode = (mode === 'en') ? 'en' : 'ar';
        if (currentSymbolMode === 'en') {
            symbolText.textContent = 'Symbols: English (F=ma)';
            symbolToggleBtn.style.color = 'var(--brand-blue)';
            symbolToggleBtn.style.borderColor = '#93c5fd';
        } else {
            symbolText.textContent = 'الرموز: عربي (ق=ك×ت)';
            symbolToggleBtn.style.color = '';
            symbolToggleBtn.style.borderColor = '';
        }
        document.querySelectorAll('.symbol-pill').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.symbol === currentSymbolMode);
        });
    }

    // 8. تصدير وطباعة المحادثة PDF
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', () => {
            window.print();
        });
    }

    // 9. البحث الفوري في المحادثات السابقة
    let searchDebounceTimer = null;
    if (convSearchInput) {
        convSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (convSearchClearBtn) convSearchClearBtn.style.display = query ? 'block' : 'none';

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => {
                filterConversationsLocally(query);
            }, 200);
        });
    }

    if (convSearchClearBtn) {
        convSearchClearBtn.addEventListener('click', () => {
            convSearchInput.value = '';
            convSearchClearBtn.style.display = 'none';
            filterConversationsLocally('');
            convSearchInput.focus();
        });
    }

    function filterConversationsLocally(query) {
        const q = query.toLowerCase();
        let visibleCount = 0;
        document.querySelectorAll('.conversation-nav-item').forEach(item => {
            const title = (item.getAttribute('data-title') || '').toLowerCase();
            const matches = !q || title.includes(q);
            item.style.display = matches ? 'flex' : 'none';
            if (matches) visibleCount++;
        });

        // إخفاء رؤوس التواريخ إذا لم يكن بها عناصر مرئية
        document.querySelectorAll('.date-group-heading').forEach(head => {
            let next = head.nextElementSibling;
            let hasVisible = false;
            while (next && !next.classList.contains('date-group-heading')) {
                if (next.classList.contains('conversation-nav-item') && next.style.display !== 'none') {
                    hasVisible = true;
                    break;
                }
                next = next.nextElementSibling;
            }
            head.style.display = hasVisible ? 'block' : 'none';
        });
    }

    // 10. بدء محادثة جديدة (New Chat)
    if (newChatBtn) {
        newChatBtn.addEventListener('click', () => {
            if (isGenerating && activeAbortController) {
                activeAbortController.abort();
            }
            currentConversationId = null;
            updateActiveConvHeader(null, 'محادثة جديدة');

            try {
                const newUrl = new URL(window.location);
                newUrl.searchParams.delete('c');
                window.history.replaceState({}, '', newUrl.pathname);
            } catch (_) {}

            removeCurrentAttachment();
            document.querySelectorAll('.conversation-nav-item').forEach(el => el.classList.remove('active-conv'));

            messagesBox.innerHTML = '';
            if (welcomeBanner) messagesBox.appendChild(welcomeBanner);
            if (typingIndicator) messagesBox.appendChild(typingIndicator);

            promptInput.value = '';
            autoResizeTextarea();
            updateSubmitButtonState();
            promptInput.focus();

            if (window.innerWidth <= 900) {
                document.body.classList.remove('sidebar-mobile-open');
            }
        });
    }

    // 11. إرفاق الملفات والمستندات والصور
    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 20 * 1024 * 1024) {
                showToast('حجم الملف أكبر من الحد المسموح (20 ميجابايت)', 'error');
                fileInput.value = '';
                return;
            }

            const fileName = file.name;
            const fileSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf' || fileName.toLowerCase().endsWith('.pdf');

            const reader = new FileReader();
            reader.onload = (event) => {
                const base64Data = event.target.result;
                attachedFilePayload = {
                    fileObj: file,
                    data: base64Data,
                    mime: file.type || (isPdf ? 'application/pdf' : 'text/plain'),
                    name: fileName,
                    size: fileSize,
                    isImage: isImage
                };

                // تحديث بطاقة المعاينة
                attachedFileName.textContent = fileName;
                attachedFileSize.textContent = fileSize;

                if (isImage) {
                    attachedThumbImg.src = base64Data;
                    attachedThumbImg.style.display = 'block';
                    attachedIcon.style.display = 'none';
                } else {
                    attachedThumbImg.style.display = 'none';
                    attachedIcon.style.display = 'block';
                    attachedIcon.className = isPdf ? 'fa-solid fa-file-pdf' : 'fa-solid fa-file-lines';
                }

                attachmentBar.style.display = 'block';
                updateSubmitButtonState();
                promptInput.focus();
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeCurrentAttachment = function() {
        attachedFilePayload = null;
        if (fileInput) fileInput.value = '';
        if (attachmentBar) attachmentBar.style.display = 'none';
        updateSubmitButtonState();
    };

    // 12. الإملاء الصوتي بالمايكروفون (Speech Recognition)
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        speechRecognitionInstance = new SpeechRec();
        speechRecognitionInstance.lang = 'ar-SA';
        speechRecognitionInstance.continuous = false;

        speechRecognitionInstance.onstart = () => {
            voiceBtn.classList.add('active-listening');
            promptInput.placeholder = 'جارٍ الاستماع إليك بالصوت... تحدث الآن';
        };

        speechRecognitionInstance.onresult = (e) => {
            const transcript = e.results[0][0].transcript;
            promptInput.value = (promptInput.value ? promptInput.value + ' ' : '') + transcript;
            autoResizeTextarea();
            updateSubmitButtonState();
        };

        speechRecognitionInstance.onerror = () => {
            voiceBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اسأل عن أي شيء، مسألة، قانون، أو موضوع...';
        };

        speechRecognitionInstance.onend = () => {
            voiceBtn.classList.remove('active-listening');
            promptInput.placeholder = 'اسأل عن أي شيء، مسألة، قانون، أو موضوع...';
        };

        if (voiceBtn) {
            voiceBtn.addEventListener('click', () => {
                try {
                    if (voiceBtn.classList.contains('active-listening')) {
                        speechRecognitionInstance.stop();
                    } else {
                        speechRecognitionInstance.start();
                    }
                } catch (err) {
                    console.warn('Speech error:', err);
                }
            });
        }
    } else if (voiceBtn) {
        voiceBtn.style.opacity = '0.5';
        voiceBtn.title = 'خاصية الإملاء الصوتي غير مدعومة في هذا المتصفح';
    }

    // 13. ضبط حقل الإدخال وزر الإرسال
    if (promptInput) {
        promptInput.addEventListener('input', () => {
            autoResizeTextarea();
            updateSubmitButtonState();
        });

        promptInput.addEventListener('keydown', (e) => {
            const enterToSend = document.getElementById('settingEnterToSend')?.checked !== false;
            if (e.key === 'Enter') {
                if (enterToSend && !e.shiftKey) {
                    e.preventDefault();
                    if (!submitBtn.disabled) handleSendMessage();
                }
            }
        });
    }

    function autoResizeTextarea() {
        if (!promptInput) return;
        promptInput.style.height = 'auto';
        promptInput.style.height = Math.min(promptInput.scrollHeight, 190) + 'px';
    }

    function updateSubmitButtonState() {
        if (!submitBtn || !promptInput) return;
        const hasText = promptInput.value.trim().length > 0;
        const hasAttachment = attachedFilePayload !== null;
        submitBtn.disabled = (!hasText && !hasAttachment) || isGenerating;
    }

    if (chatForm) {
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!submitBtn.disabled) handleSendMessage();
        });
    }

    // 14. إرسال أمر سريع من البطاقات
    window.sendQuickPrompt = function(promptText) {
        if (promptInput) {
            promptInput.value = promptText;
            autoResizeTextarea();
            updateSubmitButtonState();
            handleSendMessage();
        }
    };

    window.triggerQuickAction = function(actionType) {
        const text = promptInput.value.trim();
        let prefix = '';
        switch(actionType) {
            case 'explain': prefix = 'اشرح لي بالتفصيل خطوة بخطوة: '; break;
            case 'solve': prefix = 'حل هذه المسألة تفصيلياً مع القوانين والتعويض: '; break;
            case 'summarize': prefix = 'لخص هذا المحتوى في نقاط رئيسية وأفكار جوهرية: '; break;
            case 'quiz': prefix = 'أنشئ لي اختباراً قصيراً مكوناً من 4 أسئلة مع الإجابة النموذجية عن: '; break;
            case 'code': prefix = 'اكتب كوداً برمجياً واشرح خطواته لحل: '; break;
            case 'translate': prefix = 'ترجم هذا النص بدقة ومصطلحات أكاديمية: '; break;
        }
        promptInput.value = prefix + (text || '');
        autoResizeTextarea();
        updateSubmitButtonState();
        promptInput.focus();
    };

    // 15. معالجة إرسال الرسالة وبث الرد اللحظي (SSE Streaming)
    async function handleSendMessage(overrideText = null) {
        if (isGenerating) return;

        const text = (overrideText !== null) ? overrideText.trim() : promptInput.value.trim();
        const attachment = attachedFilePayload;

        if (!text && !attachment) return;

        // استخراج السجل السابق من الواجهة لدعم تتابع وسياق الحوار
        const existingMsgRows = Array.from(messagesBox.querySelectorAll('.chat-msg-row'));
        const clientHistory = existingMsgRows.map(row => {
            const isUser = row.classList.contains('chat-msg-user');
            const bubbleEl = row.querySelector('.msg-bubble');
            return {
                role: isUser ? 'user' : 'assistant',
                content: bubbleEl ? bubbleEl.innerText.slice(0, 800) : ''
            };
        }).filter(m => m.content.length > 0);

        const isFirstMessage = (clientHistory.length === 0);

        // إخفاء شاشة الترحيب
        if (welcomeBanner && welcomeBanner.parentNode) {
            welcomeBanner.remove();
        }

        // إضافة رسالة المستخدم إلى شاشة الدردشة
        appendUserMessage(text, attachment);

        // تصفير حقل الإدخال والمرفقات
        if (overrideText === null) {
            promptInput.value = '';
            autoResizeTextarea();
            removeCurrentAttachment();
        }

        // تفعيل وضع التوليد
        setGeneratingState(true);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            activeAbortController = new AbortController();

            // تجهيز البيانات المرسلة
            const payload = {
                prompt: text,
                conversation_id: currentConversationId,
                symbol_mode: currentSymbolMode,
                model: currentModel,
                is_first_message: isFirstMessage,
                history: clientHistory.slice(-8),
            };

            if (attachment) {
                if (attachment.isImage) {
                    payload.image_base64 = attachment.data;
                } else {
                    payload.attachment_base64 = attachment.data;
                    payload.attachment_name = attachment.name;
                    payload.attachment_mime = attachment.mime;
                }
            }

            // محاولة البث المباشر أولاً عبر Stream Endpoint
            let streamSuccess = false;
            try {
                const res = await fetch(resolveApiUrl('/ai/chat/stream'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'text/event-stream, application/json'
                    },
                    body: JSON.stringify(payload),
                    signal: activeAbortController.signal
                });

                if (res.ok && res.body) {
                    const contentType = res.headers.get('content-type') || '';
                    if (contentType.includes('text/event-stream')) {
                        streamSuccess = true;
                        typingIndicator.style.display = 'none';

                        const streamCard = createStreamingAssistantCard();
                        const reader = res.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';
                        let accumulatedReply = '';

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            const lines = buffer.split('\n');
                            buffer = lines.pop();

                            for (const line of lines) {
                                const trimmedLine = line.trim();
                                if (trimmedLine.startsWith('data: ')) {
                                    try {
                                        const data = JSON.parse(trimmedLine.slice(6));
                                        if (data.type === 'init') {
                                            currentConversationId = data.conversation_id;
                                            updateActiveConvHeader(data.conversation_id, data.conversation_title);
                                            updateUrlWithConvId(data.conversation_id);
                                            updateSidebarNavList(data.conversation_id, data.conversation_title, currentSymbolMode);
                                        } else if (data.type === 'chunk') {
                                            accumulatedReply += data.chunk;
                                            streamCard.update(accumulatedReply);
                                            scrollFeedToBottom();
                                        } else if (data.type === 'done') {
                                            streamCard.finalize(data.final_text || accumulatedReply, data.provider);
                                            scrollFeedToBottom();
                                        }
                                    } catch (_) {}
                                }
                            }
                        }

                        if (!streamCard.isFinalized) {
                            streamCard.finalize(accumulatedReply, 'gemini');
                        }
                    }
                }
            } catch (streamErr) {
                if (streamErr.name === 'AbortError') {
                    showToast('تم إيقاف التوليد بناءً على طلبك ⏹️', 'success');
                    return;
                }
                console.warn('Stream failed or fallback requested:', streamErr);
            }

            // الرجوع التلقائي للاستدعاء العادي في حال تعذر الـ Stream
            if (!streamSuccess) {
                const res = await fetch(resolveApiUrl('/ai/chat'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    signal: activeAbortController.signal
                });

                if (!res.ok) {
                    let errText = 'خطأ في معالجة الطلب (' + res.status + ')';
                    try {
                        const errJson = await res.json();
                        if (errJson && errJson.message) errText = errJson.message;
                    } catch (_) {}
                    appendAssistantMessage('⚠️ ' + errText, 'error');
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    currentConversationId = data.conversation_id;
                    updateActiveConvHeader(data.conversation_id, data.conversation_title);
                    updateUrlWithConvId(data.conversation_id);
                    appendAssistantMessage(data.reply, data.provider);
                    updateSidebarNavList(data.conversation_id, data.conversation_title, data.symbol_mode);
                } else {
                    appendAssistantMessage('عذراً، حدث خطأ: ' + (data.message || 'يرجى المحاولة مجدداً'), 'error');
                }
            }

        } catch (error) {
            if (error.name === 'AbortError') {
                showToast('تم إيقاف التوليد ⏹️', 'success');
            } else {
                console.error('Chat AI Error:', error);
                appendAssistantMessage('⚠️ تعذر الاتصال بمحرك الذكاء الاصطناعي حالياً. يرجى المحاولة مرة أخرى.', 'error');
            }
        } finally {
            setGeneratingState(false);
            activeAbortController = null;
            scrollFeedToBottom();
        }
    }

    // زر إيقاف التوليد (Stop Generating)
    if (btnStopGenerating) {
        btnStopGenerating.addEventListener('click', () => {
            if (activeAbortController) {
                activeAbortController.abort();
            }
        });
    }

    function setGeneratingState(generating) {
        isGenerating = generating;
        typingIndicator.style.display = generating ? 'flex' : 'none';
        stopGeneratingDock.style.display = generating ? 'block' : 'none';
        updateSubmitButtonState();
        if (generating) scrollFeedToBottom();
    }

    function updateUrlWithConvId(id) {
        try {
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('c', id);
            window.history.replaceState({ convId: id }, '', newUrl.toString());
        } catch (_) {}
    }

    // 16. تنسيق نصوص Markdown ومعادلات LaTeX باحترافية
    function formatMd(text) {
        if (!text) return '';

        // تنظيف علامات الدولار الموضوعة خطأً حول نصوص عربية
        text = text.replace(/\$\$\s*([\u0600-\u06FF\s\u064B-\u065F\(\)\.\،\-]+)\s*\$\$/g, '$1');
        text = text.replace(/\$([\u0600-\u06FF\s\u064B-\u065F\(\)\.\،\-]+)\$/g, '$1');
        text = text.replace(/\(\s*([\u0600-\u06FF])\s*\)/g, '($1)');

        if (typeof marked === 'undefined') {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }

        try {
            const mathTokens = [];
            let processed = text.replace(/\$\$([\s\S]*?)\$\$/g, (match) => {
                const id = `@@MATH_BLOCK_${mathTokens.length}@@`;
                mathTokens.push({ id, original: match });
                return id;
            });
            processed = processed.replace(/\\\[([\s\S]*?)\\\]/g, (match) => {
                const id = `@@MATH_BLOCK_${mathTokens.length}@@`;
                mathTokens.push({ id, original: match });
                return id;
            });
            processed = processed.replace(/\$([^\$\n]+?)\$/g, (match) => {
                const id = `@@MATH_INLINE_${mathTokens.length}@@`;
                mathTokens.push({ id, original: match });
                return id;
            });
            processed = processed.replace(/\\\(([^\(\n]+?)\\\)/g, (match) => {
                const id = `@@MATH_INLINE_${mathTokens.length}@@`;
                mathTokens.push({ id, original: match });
                return id;
            });

            // ضبط marked لتوليد بطاقات الأكواد مع زر نسخ مستقل
            const renderer = new marked.Renderer();
            renderer.code = function(code, language) {
                const lang = language || 'code';
                const codeId = 'code_' + Math.random().toString(36).substring(2, 9);
                return `
                    <div class="code-block-wrapper">
                        <div class="code-header-bar">
                            <span class="code-lang-label">${escapeHtml(lang)}</span>
                            <button type="button" class="btn-copy-code" onclick="copyCodeSnippet('${codeId}')">
                                <i class="fa-regular fa-copy"></i> نسخ الكود
                            </button>
                        </div>
                        <pre><code id="${codeId}" class="language-${escapeHtml(lang)}">${escapeHtml(code)}</code></pre>
                    </div>
                `;
            };

            let parsedHtml = marked.parse(processed, { renderer: renderer });

            // استعادة رموز الرياضيات
            mathTokens.forEach(item => {
                parsedHtml = parsedHtml.replace(item.id, item.original);
            });

            return parsedHtml;
        } catch (e) {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }
    }

    // 17. بطاقة بث الرد في الوقت الفعلي
    function createStreamingAssistantCard() {
        const msgRow = document.createElement('div');
        msgRow.className = 'chat-msg-row chat-msg-assistant';
        const msgId = 'aiMsg_' + Date.now();

        msgRow.innerHTML = `
            <div class="msg-avatar">
                <img src="{{ asset('logo.png') }}" alt="AI" class="msg-avatar-ai-img" onerror="this.src='{{ asset('images/logo.png') }}'">
            </div>
            <div class="msg-body-wrapper">
                <div class="msg-author-time">
                    <span>Step by Step Ai</span>
                    <span>&bull; ${getNowTimeString()}</span>
                </div>
                <div class="msg-bubble" id="${msgId}"><span class="typing-cursor">▌</span></div>
                <div class="msg-actions-row" style="opacity: 0; transition: opacity 0.3s ease;">
                    <button type="button" class="btn-msg-action" title="نسخ الإجابة" onclick="copyAiReply('${msgId}')">
                        <i class="fa-regular fa-copy"></i> نسخ
                    </button>
                    <button type="button" class="btn-msg-action" title="إعادة التوليد" onclick="regenerateLastReply()">
                        <i class="fa-solid fa-rotate-right"></i> إعادة التوليد
                    </button>
                    <button type="button" class="btn-msg-action" title="إعجاب" onclick="rateAiReply('${msgId}', 'like', this)">
                        <i class="fa-regular fa-thumbs-up"></i>
                    </button>
                    <button type="button" class="btn-msg-action" title="لم يعجبني" onclick="rateAiReply('${msgId}', 'dislike', this)">
                        <i class="fa-regular fa-thumbs-down"></i>
                    </button>
                    <button type="button" class="btn-msg-action" title="استماع صوتي" onclick="speakAiReply('${msgId}')">
                        <i class="fa-solid fa-volume-high"></i> استماع
                    </button>
                </div>
            </div>
        `;

        messagesBox.insertBefore(msgRow, typingIndicator);
        scrollFeedToBottom();

        const bubble = msgRow.querySelector('.msg-bubble');
        const actionsRow = msgRow.querySelector('.msg-actions-row');
        let isFinalized = false;
        let lastRenderTime = 0;

        return {
            msgId,
            get isFinalized() { return isFinalized; },
            update(text) {
                bubble.innerHTML = formatMd(text) + '<span class="typing-cursor">▌</span>';
                const now = Date.now();
                if (now - lastRenderTime > 250) {
                    lastRenderTime = now;
                    renderMathInBubble(bubble);
                }
            },
            finalize(finalText, provider) {
                isFinalized = true;
                bubble.innerHTML = formatMd(finalText);
                renderMathInBubble(bubble);
                actionsRow.style.opacity = '1';
                scrollFeedToBottom();
            }
        };
    }

    // 18. إضافة رسالة المستخدم
    function appendUserMessage(text, attachment) {
        const msgRow = document.createElement('div');
        msgRow.className = 'chat-msg-row chat-msg-user';
        const userMsgId = 'userMsg_' + Date.now();

        let attachContent = '';
        if (attachment) {
            if (attachment.isImage) {
                attachContent = `<img src="${attachment.data}" alt="${escapeHtml(attachment.name)}">`;
            } else {
                attachContent = `
                    <div class="user-file-attachment-badge">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>${escapeHtml(attachment.name)} (${escapeHtml(attachment.size)})</span>
                    </div>
                `;
            }
        }

        let textHtml = text ? `<div>${escapeHtml(text).replace(/\n/g, '<br>')}</div>` : '';

        msgRow.innerHTML = `
            <div class="msg-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="msg-body-wrapper">
                <div class="msg-author-time">
                    <span>أنت</span>
                    <span>&bull; ${getNowTimeString()}</span>
                </div>
                <div class="msg-bubble" id="${userMsgId}">
                    ${attachContent}
                    ${textHtml}
                </div>
                <div class="msg-actions-row" style="justify-content: flex-end;">
                    <button type="button" class="btn-msg-action" title="تعديل السؤال" onclick="editUserMessage('${escapeHtml(text)}')">
                        <i class="fa-solid fa-pen"></i> تعديل السؤال
                    </button>
                </div>
            </div>
        `;

        messagesBox.insertBefore(msgRow, typingIndicator);
        scrollFeedToBottom();
    }

    // 19. إضافة رد الذكاء الاصطناعي دفعة واحدة (Fallback)
    function appendAssistantMessage(rawText, provider) {
        const msgRow = document.createElement('div');
        msgRow.className = 'chat-msg-row chat-msg-assistant';
        const msgId = 'aiMsg_' + Date.now();

        msgRow.innerHTML = `
            <div class="msg-avatar">
                <img src="{{ asset('logo.png') }}" alt="AI" class="msg-avatar-ai-img" onerror="this.src='{{ asset('images/logo.png') }}'">
            </div>
            <div class="msg-body-wrapper">
                <div class="msg-author-time">
                    <span>Step by Step Ai</span>
                    <span>&bull; ${getNowTimeString()}</span>
                </div>
                <div class="msg-bubble" id="${msgId}"></div>
                <div class="msg-actions-row">
                    <button type="button" class="btn-msg-action" title="نسخ الإجابة" onclick="copyAiReply('${msgId}')">
                        <i class="fa-regular fa-copy"></i> نسخ
                    </button>
                    <button type="button" class="btn-msg-action" title="إعادة التوليد" onclick="regenerateLastReply()">
                        <i class="fa-solid fa-rotate-right"></i> إعادة التوليد
                    </button>
                    <button type="button" class="btn-msg-action" title="إعجاب" onclick="rateAiReply('${msgId}', 'like', this)">
                        <i class="fa-regular fa-thumbs-up"></i>
                    </button>
                    <button type="button" class="btn-msg-action" title="لم يعجبني" onclick="rateAiReply('${msgId}', 'dislike', this)">
                        <i class="fa-regular fa-thumbs-down"></i>
                    </button>
                    <button type="button" class="btn-msg-action" title="استماع صوتي" onclick="speakAiReply('${msgId}')">
                        <i class="fa-solid fa-volume-high"></i> استماع
                    </button>
                </div>
            </div>
        `;

        messagesBox.insertBefore(msgRow, typingIndicator);
        scrollFeedToBottom();

        const bubble = msgRow.querySelector('.msg-bubble');
        bubble.innerHTML = formatMd(rawText);
        renderMathInBubble(bubble);
        scrollFeedToBottom();
    }

    // 20. تصيير معادلات KaTeX
    function renderMathInBubble(element) {
        if (!element || typeof renderMathInElement === 'undefined') return;
        try {
            renderMathInElement(element, {
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

    // 21. أدوات التفاعل مع الرسائل (Copy, TTS, Feedback, Edit, Regenerate)
    window.copyAiReply = function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const text = el.innerText || el.textContent;
        navigator.clipboard.writeText(text).then(() => {
            showToast('تم نسخ الإجابة بنجاح ✅', 'success');
        }).catch(() => {
            alert('يرجى تحديد النص ونسخه يدوياً.');
        });
    };

    window.copyCodeSnippet = function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const text = el.innerText || el.textContent;
        navigator.clipboard.writeText(text).then(() => {
            showToast('تم نسخ الكود البرمجي بنجاح 📋', 'success');
        });
    };

    window.speakAiReply = function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const text = el.innerText || el.textContent;
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ar-SA';
            utterance.rate = 0.95;
            window.speechSynthesis.speak(utterance);
            showToast('جارٍ تشغيل القراءة الصوتية للإجابة 🔊', 'success');
        } else {
            alert('المتصفح لا يدعم النطق الصوتي.');
        }
    };

    window.rateAiReply = async function(id, type, btnEl) {
        btnEl.classList.toggle('active-liked', type === 'like');
        btnEl.classList.toggle('active-disliked', type === 'dislike');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch(resolveApiUrl('/ai/feedback'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({ message_id: id, type: type })
            });
            showToast(type === 'like' ? 'شكراً لتقييمك الإيجابي! 👍' : 'شكراً لملاحظتك، سنعمل على تحسين الدقة ✍️', 'success');
        } catch (_) {}
    };

    window.editUserMessage = function(originalText) {
        promptInput.value = originalText;
        autoResizeTextarea();
        updateSubmitButtonState();
        promptInput.focus();
        showToast('يمكنك تعديل السؤال الآن ثم إرساله مجدداً ✏️', 'success');
    };

    window.regenerateLastReply = function() {
        const userRows = messagesBox.querySelectorAll('.chat-msg-user');
        if (userRows.length === 0) return;
        const lastUserBubble = userRows[userRows.length - 1].querySelector('.msg-bubble');
        const text = lastUserBubble ? lastUserBubble.innerText.trim() : '';
        if (text) {
            handleSendMessage(text);
        }
    };

    // 22. إدارة المحادثات (تسمية وحفظ وحذف)
    let modalTargetConvId = null;

    function updateActiveConvHeader(id, title) {
        const titleEl = document.getElementById('stepActiveConvTitle');
        const deleteBtn = document.getElementById('stepDeleteCurrentBtn');
        if (titleEl) {
            titleEl.textContent = title || 'محادثة جديدة';
            titleEl.title = title || 'محادثة جديدة';
        }
        if (deleteBtn) {
            deleteBtn.style.display = id ? 'inline-flex' : 'none';
        }
    }

    window.openRenameModalForCurrent = function() {
        modalTargetConvId = currentConversationId;
        const modal = document.getElementById('stepRenameModalOverlay');
        const input = document.getElementById('renameModalInput');
        const modalTitle = document.getElementById('renameModalTitleText');
        const activeTitle = document.getElementById('stepActiveConvTitle')?.textContent || '';

        input.value = (activeTitle !== 'محادثة جديدة') ? activeTitle : '';
        modalTitle.textContent = modalTargetConvId ? 'تعديل اسم المحادثة' : 'تسمية وحفظ المحادثة';

        modal.classList.add('active');
        setTimeout(() => input.focus(), 100);
    };

    window.openRenameModalForId = function(id, event) {
        if (event && event.stopPropagation) event.stopPropagation();
        modalTargetConvId = id;
        const modal = document.getElementById('stepRenameModalOverlay');
        const input = document.getElementById('renameModalInput');
        const modalTitle = document.getElementById('renameModalTitleText');

        const item = document.querySelector(`.conversation-nav-item[data-id="${id}"]`);
        const itemTitle = item ? (item.getAttribute('data-title') || '') : '';

        input.value = itemTitle;
        modalTitle.textContent = 'تعديل اسم المحادثة';

        modal.classList.add('active');
        setTimeout(() => input.focus(), 100);
    };

    window.closeRenameModal = function() {
        const modal = document.getElementById('stepRenameModalOverlay');
        if (modal) modal.classList.remove('active');
        modalTargetConvId = null;
    };

    window.setSuggestedTitle = function(title) {
        const input = document.getElementById('renameModalInput');
        if (input) {
            input.value = title;
            input.focus();
        }
    };

    const saveRenameBtn = document.getElementById('btnSaveRenameModal');
    if (saveRenameBtn) {
        saveRenameBtn.addEventListener('click', async () => {
            const input = document.getElementById('renameModalInput');
            const newTitle = input.value.trim();
            if (!newTitle) {
                alert('يرجى كتابة اسم للمحادثة.');
                input.focus();
                return;
            }

            saveRenameBtn.disabled = true;
            saveRenameBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> جارٍ الحفظ...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (modalTargetConvId) {
                    const res = await fetch(resolveApiUrl('/ai/conversations/' + modalTargetConvId + '/rename'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: newTitle })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const item = document.querySelector(`.conversation-nav-item[data-id="${modalTargetConvId}"]`);
                        if (item) {
                            item.setAttribute('data-title', newTitle);
                            const textEl = item.querySelector('.conv-title-text');
                            if (textEl) { textEl.textContent = newTitle; textEl.title = newTitle; }
                        }
                        if (currentConversationId == modalTargetConvId) {
                            updateActiveConvHeader(modalTargetConvId, newTitle);
                        }
                        closeRenameModal();
                        showToast('تم تعديل اسم المحادثة بنجاح ✅', 'success');
                    }
                } else {
                    const res = await fetch(resolveApiUrl('/ai/conversations'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title: newTitle, symbol_mode: currentSymbolMode })
                    });
                    const data = await res.json();
                    if (data.success && data.conversation) {
                        currentConversationId = data.conversation.id;
                        updateActiveConvHeader(data.conversation.id, data.conversation.title);
                        updateUrlWithConvId(data.conversation.id);
                        updateSidebarNavList(data.conversation.id, data.conversation.title, data.conversation.symbol_mode);
                        closeRenameModal();
                        showToast('تم حفظ المحادثة بنجاح ✅', 'success');
                    }
                }
            } catch (err) {
                alert('حدث خطأ أثناء حفظ المحادثة.');
            } finally {
                saveRenameBtn.disabled = false;
                saveRenameBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> <span>حفظ الاسم</span>';
            }
        });
    }

    // تأكيد الحذف (Delete Modal)
    let deleteTargetId = null;

    window.openDeleteModalForCurrent = function() {
        if (!currentConversationId) return;
        openDeleteModal(currentConversationId, document.getElementById('stepActiveConvTitle')?.textContent || '');
    };

    window.openDeleteModalForId = function(id, event) {
        if (event && event.stopPropagation) event.stopPropagation();
        const item = document.querySelector(`.conversation-nav-item[data-id="${id}"]`);
        const itemTitle = item ? (item.getAttribute('data-title') || '') : '';
        openDeleteModal(id, itemTitle);
    };

    function openDeleteModal(id, title) {
        deleteTargetId = id;
        document.getElementById('deleteTargetConvTitle').textContent = `"${title}"`;
        document.getElementById('stepDeleteModalOverlay').classList.add('active');
    }

    window.closeDeleteModal = function() {
        document.getElementById('stepDeleteModalOverlay').classList.remove('active');
        deleteTargetId = null;
    };

    const confirmDeleteBtn = document.getElementById('btnConfirmDeleteModal');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async () => {
            if (!deleteTargetId) return;
            const id = deleteTargetId;
            confirmDeleteBtn.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(resolveApiUrl('/ai/conversations/' + id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const item = document.querySelector(`.conversation-nav-item[data-id="${id}"]`);
                    if (item) {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        setTimeout(() => item.remove(), 200);
                    }
                    if (convCountBadge) {
                        convCountBadge.textContent = Math.max(0, parseInt(convCountBadge.textContent || '1') - 1);
                    }
                    if (currentConversationId == id) {
                        newChatBtn.click();
                    }
                    closeDeleteModal();
                    showToast('تم حذف المحادثة بنجاح 🗑️', 'success');
                }
            } catch (err) {
                alert('تعذر حذف المحادثة.');
            } finally {
                confirmDeleteBtn.disabled = false;
            }
        });
    }

    // 23. تحميل محادثة سابقة مع رسائلها
    window.loadConversation = async function(id) {
        currentConversationId = id;

        document.querySelectorAll('.conversation-nav-item').forEach(el => {
            el.classList.toggle('active-conv', el.getAttribute('data-id') == id);
        });

        if (window.innerWidth <= 900) {
            document.body.classList.remove('sidebar-mobile-open');
        }

        updateUrlWithConvId(id);

        messagesBox.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: var(--ai-text-muted);">
                <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 2rem; color: var(--brand-blue);"></i>
                <p style="margin-top: 12px; font-weight: 700;">جارٍ استرجاع سجل المحادثة...</p>
            </div>
        `;

        try {
            const res = await fetch(resolveApiUrl('/ai/conversations/' + id));
            const data = await res.json();

            messagesBox.innerHTML = '';
            messagesBox.appendChild(typingIndicator);

            if (data.conversation) {
                updateActiveConvHeader(data.conversation.id, data.conversation.title);
                if (data.conversation.symbol_mode) {
                    setSymbolMode(data.conversation.symbol_mode);
                }
            }

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.role === 'user') {
                        appendUserMessage(msg.content, null);
                    } else {
                        appendAssistantMessage(msg.content, null);
                    }
                });
            } else {
                messagesBox.innerHTML = `<p style="text-align: center; color: var(--ai-text-muted); padding: 40px;">المحادثة فارغة، اطرح سؤالك الآن!</p>`;
                messagesBox.appendChild(typingIndicator);
            }
        } catch (e) {
            messagesBox.innerHTML = `<p style="text-align: center; color: #ef4444; padding: 40px;">تعذر تحميل رسائل المحادثة.</p>`;
            messagesBox.appendChild(typingIndicator);
        }
    };

    // 24. تحديث القائمة الجانبية عند بدء محادثة جديدة
    function updateSidebarNavList(id, title, symbolMode) {
        const existing = document.querySelector(`.conversation-nav-item[data-id="${id}"]`);
        if (existing) {
            existing.classList.add('active-conv');
            existing.setAttribute('data-title', title);
            const titleEl = existing.querySelector('.conv-title-text');
            if (titleEl) { titleEl.textContent = title; titleEl.title = title; }
            return;
        }

        const emptyNotice = document.getElementById('stepEmptyConvsNotice');
        if (emptyNotice) emptyNotice.remove();

        const newItem = document.createElement('div');
        newItem.className = 'conversation-nav-item active-conv';
        newItem.setAttribute('data-id', id);
        newItem.setAttribute('data-title', title);
        newItem.innerHTML = `
            <div class="conversation-main-click" onclick="loadConversation('${id}')">
                <i class="fa-regular fa-message conv-msg-icon" style="color: var(--brand-blue);"></i>
                <div class="conv-info">
                    <span class="conv-title-text" title="${escapeHtml(title)}">${escapeHtml(title)}</span>
                    <span class="conv-meta-date">
                        الآن &bull; <span class="mode-tag">${symbolMode === 'en' ? 'EN' : 'عربي'}</span>
                    </span>
                </div>
            </div>
            <div class="conv-actions-group">
                <button type="button" class="btn-conv-action btn-rename-conv" title="إعادة تسمية" onclick="openRenameModalForId('${id}', event)">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button type="button" class="btn-conv-action btn-delete-conv" title="حذف" onclick="openDeleteModalForId('${id}', event)">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>
        `;

        const todayHeading = conversationsList.querySelector('.date-group-heading');
        if (todayHeading) {
            conversationsList.insertBefore(newItem, todayHeading.nextSibling);
        } else {
            conversationsList.insertBefore(newItem, conversationsList.firstChild);
        }

        if (convCountBadge) {
            convCountBadge.textContent = parseInt(convCountBadge.textContent || '0') + 1;
        }
    }

    // 25. مسح كافة السجلات
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', async () => {
            if (!confirm('هل تريد مسح كافة سجلات المحادثات السابقة نهائياً؟')) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(resolveApiUrl('/ai/conversations'), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    conversationsList.innerHTML = `
                        <div class="empty-conversations-state" id="stepEmptyConvsNotice">
                            <div class="empty-icon-wrap"><i class="fa-regular fa-comments"></i></div>
                            <p>لا توجد محادثات سابقة، ابدأ استفسارك الأول الآن!</p>
                        </div>
                    `;
                    if (convCountBadge) convCountBadge.textContent = '0';
                    newChatBtn.click();
                    showToast('تم مسح سجل المحادثات بالكامل 🧹', 'success');
                }
            } catch (e) {
                alert('تعذر مسح المحادثات.');
            }
        });
    }

    // 26. نافذة الإعدادات (Settings Modal)
    if (settingsBtn) {
        settingsBtn.addEventListener('click', () => {
            document.getElementById('stepSettingsModalOverlay').classList.add('active');
        });
    }

    window.closeSettingsModal = function() {
        document.getElementById('stepSettingsModalOverlay').classList.remove('active');
    };

    document.querySelectorAll('.settings-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.settings-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.settings-tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            const tabId = 'tabContent_' + btn.dataset.tab;
            const targetContent = document.getElementById(tabId);
            if (targetContent) targetContent.classList.add('active');
        });
    });

    document.querySelectorAll('.theme-pill').forEach(btn => {
        btn.addEventListener('click', () => {
            applyTheme(btn.dataset.theme);
        });
    });

    document.querySelectorAll('.symbol-pill').forEach(btn => {
        btn.addEventListener('click', () => {
            setSymbolMode(btn.dataset.symbol);
        });
    });

    // دوال مساعدة
    function scrollFeedToBottom() {
        if (messagesBox) messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function getNowTimeString() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('stepToastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `step-toast ${type}`;
        toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> <span>${escapeHtml(message)}</span>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.2s';
            setTimeout(() => toast.remove(), 200);
        }, 3200);
    };

    // فحص الرابط لاسترجاع المحادثة تلقائياً (?c=123)
    const urlParams = new URLSearchParams(window.location.search);
    const initialConvId = urlParams.get('c');
    if (initialConvId) {
        loadConversation(initialConvId);
    }

})();
</script>
@endsection
