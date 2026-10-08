@extends('layouts.app')

@section('title', __('إدارة ورفع الملازم والدوسيات والملفات') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="ed-teacher-files-container">

    <!-- الهيدر الأكاديمي الرسمي -->
    <header class="ed-teacher-header">
        <div>
            <div class="ed-teacher-breadcrumbs">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('teacher.dashboard') }}">
                    {{ auth()->user()->role === 'admin' ? __('لوحة الإدارة') : __('بوابة المعلم المعتمد') }}
                </a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                <span class="active">{{ __('الملازم والدوسيات التعليمية') }}</span>
            </div>
            <h1>
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i>
                {{ __('إدارة ورفع الملازم والدوسيات وأوراق العمل') }}
            </h1>
            <p>
                {{ __('رفع وتوثيق الدوسيات الوزارية الشاملة، ملخصات الوحدات، أوراق العمل، وبنوك الأسئلة المجابة بصيغ PDF والملفات المعتمدة للتحميل المباشر للطلبة.') }}
            </p>
        </div>

        <div class="header-actions" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if(in_array(auth()->user()->role, ['admin', 'teacher']))
                <button type="button" onclick="purgeAllContents()" class="ed-btn-purge" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.08);" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>{{ auth()->user()->role === 'admin' ? __('حذف وتصفير كافة المحتويات دفعة واحدة') : __('حذف وتصفير كافة محتوياتي دفعة واحدة') }}</span>
                </button>
            @endif
            <button type="button" onclick="openUploadFileModal()" class="ed-btn-upload-file">
                <i class="fa-solid fa-file-arrow-up"></i>
                <span>{{ __('رفع ملزمة / دوسية جديدة') }}</span>
            </button>
        </div>
    </header>

    <!-- شريط الإحصائيات والمؤشرات السريعة -->
    <div class="ed-stats-strip">
        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div>
                <span class="stat-num">{{ $stats['total'] ?? $files->total() }}</span>
                <span class="stat-label">{{ __('إجمالي الدوسيات والملفات') }}</span>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-cloud-arrow-down"></i>
            </div>
            <div>
                <span class="stat-num">{{ $stats['visible'] ?? 0 }}</span>
                <span class="stat-label">{{ __('ملفات متاحة للتحميل الفوري') }}</span>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon-wrap" style="background: #eff6ff; color: #1d4ed8;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span class="stat-num">100%</span>
                <span class="stat-label">{{ __('تخزين سحابي موثوق وآمن') }}</span>
            </div>
        </div>
    </div>

    <!-- شريط تصفية الفروع للمدير العام -->
    @if(auth()->user()->role === 'admin' && isset($stages) && count($stages) > 1)
        <div class="ed-filter-bar" style="margin-bottom: 8px;">
            <div class="filter-label">
                <i class="fa-solid fa-code-branch"></i>
                <span>{{ __('تصفية حسب الفرع الدراسي:') }}</span>
            </div>
            <div class="filter-pills">
                <a href="{{ route('admin.files', request()->except(['stage_id', 'page'])) }}" class="filter-chip {{ empty(request('stage_id')) ? 'active' : '' }}">
                    {{ __('كافة الفروع 🏫') }}
                </a>
                @foreach($stages as $stg)
                    <a href="{{ route('admin.files', array_merge(request()->except(['stage_id', 'page']), ['stage_id' => $stg->id])) }}" class="filter-chip {{ request('stage_id') == $stg->id ? 'active' : '' }}">
                        {{ $stg->label_ar }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- شريط التصفية حسب المادة -->
    @if(auth()->user()->role === 'admin' || count($subjects) > 1)
        <div class="ed-filter-bar">
            <div class="filter-label">
                <i class="fa-solid fa-filter"></i>
                <span>{{ __('تصفية حسب المادة الدراسية:') }}</span>
            </div>
            <div class="filter-pills">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files') }}" class="filter-chip {{ empty(request('subject_id')) && !request('show_all') ? 'active' : '' }}">
                    {{ auth()->user()->role === 'admin' ? __('جميع المواد') : __('جميع موادي') }}
                </a>
                @if(auth()->user()->role === 'teacher')
                    <a href="{{ route('teacher.files', ['show_all' => '1']) }}" class="filter-chip {{ request('show_all') == '1' ? 'active' : '' }}" style="{{ request('show_all') == '1' ? 'background: #1e3a8a; border-color: #1e3a8a; color: #fff;' : '' }}">
                        🌐 {{ __('كافة ملفات ودوسيات المنصة (المدير والمصور والمعلمين)') }}
                    </a>
                @endif
                @foreach($subjects as $sub)
                    <a href="{{ (auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files')) . '?subject_id=' . $sub->id . (request('target_region') ? '&target_region=' . request('target_region') : '') }}" class="filter-chip {{ request('subject_id') == $sub->id ? 'active' : '' }}">
                        {{ $sub->name_ar ?? $sub->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- شريط تصفية الجمهور والمنطقة (غزة / الضفة) للملفات -->
    <div class="ed-filter-bar" style="margin-top: {{ (auth()->user()->role === 'admin' || count($subjects) > 1) ? '10px' : '0' }};">
        <div class="filter-label">
            <i class="fa-solid fa-map-location-dot"></i>
            <span>{{ __('الجمهور المستهدف:') }}</span>
        </div>
        <div class="filter-pills">
            @php
                $baseRouteFiles = auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files');
                $subFileQuery = request('subject_id') ? '&subject_id=' . request('subject_id') : '';
            @endphp
            <a href="{{ $baseRouteFiles . '?' . ltrim($subFileQuery, '&') }}" class="filter-chip {{ empty(request('target_region')) ? 'active' : '' }}">
                {{ __('كافة الملازم والدوسيات 🌐') }}
            </a>
            <a href="{{ $baseRouteFiles . '?target_region=gaza' . $subFileQuery }}" class="filter-chip {{ request('target_region') === 'gaza' ? 'active' : '' }}" style="{{ request('target_region') === 'gaza' ? 'background: #059669; border-color: #059669; color: #fff;' : '' }}">
                🌿 {{ __('قطاع غزة') }}
            </a>
            <a href="{{ $baseRouteFiles . '?target_region=west_bank' . $subFileQuery }}" class="filter-chip {{ request('target_region') === 'west_bank' ? 'active' : '' }}" style="{{ request('target_region') === 'west_bank' ? 'background: #1e40af; border-color: #1e40af; color: #fff;' : '' }}">
                🏛️ {{ __('الضفة والقدس') }}
            </a>
            <a href="{{ $baseRouteFiles . '?target_region=all' . $subFileQuery }}" class="filter-chip {{ request('target_region') === 'all' ? 'active' : '' }}">
                🌐 {{ __('منهاج مشترك') }}
            </a>
        </div>
    </div>

    <!-- شبكة بطاقات الملفات والدوسيات -->
    <div class="ed-files-grid">
        @forelse($files as $file)
            @php
                $meta = $file->file_meta ?? [
                    'icon' => 'fa-solid fa-file-pdf',
                    'color' => '#ef4444',
                    'bg' => '#fef2f2',
                    'label' => 'PDF'
                ];
            @endphp
            <div class="ed-file-card" id="file_card_{{ $file->id }}">
                <div>
                    <div class="file-card-header">
                        <div class="file-icon-box" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }};">
                            <i class="{{ $meta['icon'] }}"></i>
                        </div>
                        <div style="overflow: hidden; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 4px;">
                                <span class="file-tag">
                                    {{ $file->subject?->name_ar ?? __('عام') }} • {{ $meta['label'] }}
                                </span>
                                @php
                                    $frBadge = $file->target_region_badge;
                                @endphp
                                <span style="background: {{ $frBadge['bg'] }}; color: {{ $frBadge['color'] }}; border: 1px solid {{ $frBadge['border'] }}; padding: 2px 7px; border-radius: 6px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="{{ $frBadge['icon'] }}"></i>
                                    <span>{{ $frBadge['label'] }}</span>
                                </span>
                            </div>
                            <h3 class="file-title" title="{{ $file->title }}">
                                {{ $file->title }}
                            </h3>
                        </div>
                    </div>

                    <div class="file-meta-box">
                        <div>
                            <i class="fa-solid fa-folder-open" style="color: #f59e0b;"></i>
                            <strong>{{ __('التصنيف:') }}</strong>
                            <span>{{ $file->channel_name ?? __('دوسية / ملزمة معتمدة') }}</span>
                        </div>
                        <div>
                            <i class="fa-solid fa-weight-hanging" style="color: #1e3a8a;"></i>
                            <strong>{{ __('حجم الملف:') }}</strong>
                            <span class="font-mono">{{ $file->file_size ?? __('غير محدد') }}</span>
                        </div>
                        <div>
                            <i class="fa-solid fa-arrow-down-1-9" style="color: #64748b;"></i>
                            <strong>{{ __('الترتيب:') }}</strong>
                            <span class="font-mono">#{{ $file->order }}</span>
                        </div>
                    </div>
                </div>

                <div class="file-card-footer">
                    <a href="{{ route('content.download', $file->id) }}" target="_blank" class="btn-download">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                        <span>{{ __('تحميل الملف') }}</span>
                    </a>

                    <div class="footer-control-side">
                        <button type="button" onclick="toggleVisibility({{ $file->id }}, this)" class="visibility-btn {{ $file->is_visible ? 'is-visible' : 'is-hidden' }}" title="{{ __('تبديل الإتاحة للطلبة') }}">
                            <i class="fa-solid {{ $file->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        </button>

                        <div class="action-btns">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.educational_contents.edit', $file->id) }}" class="btn-edit" title="{{ __('تعديل') }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @else
                                <a href="{{ route('teacher.educational_contents.edit', $file->id) }}" class="btn-edit" title="{{ __('تعديل') }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            @endif

                            @php
                                $fileDeleteUrl = auth()->user()->role === 'admin' 
                                    ? route('admin.educational_contents.destroy', $file->id) 
                                    : route('teacher.educational_contents.destroy', $file->id);
                            @endphp
                            <form id="delete_form_file_{{ $file->id }}" action="{{ $fileDeleteUrl }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذه الملزمة؟') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="deleteFileItem({{ $file->id }}, event)" class="btn-delete" title="{{ __('حذف') }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="ed-empty-files">
                <i class="fa-solid fa-folder-open"></i>
                <h3>{{ __('لا توجد ملازم أو دوسيات مرفوعة حالياً') }}</h3>
                <p>{{ __('انقر على زر "رفع ملزمة / دوسية جديدة" لمشاركة المذكرات والملخصات وأوراق العمل مع طلبتك.') }}</p>
                <button type="button" onclick="openUploadFileModal()" class="ed-btn-upload-file" style="margin: 0 auto;">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    <span>{{ __('رفع أول ملزمة الآن') }}</span>
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px;">
        {{ $files->links() }}
    </div>

</div>

<!-- نافذة Modal رفع ملزمة / دوسية جديدة -->
<div id="uploadFileModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3>
                <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i>
                <span>{{ __('رفع وتوثيق ملزمة أو دوسية جديدة') }}</span>
            </h3>
            <button type="button" onclick="closeUploadFileModal()" class="btn-close-modal">&times;</button>
        </div>

        <form id="uploadDocForm" onsubmit="submitDocForm(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="file">

            <div class="modal-form-body">
                @if(auth()->user()->role === 'admin' && isset($stages) && count($stages) > 0)
                    <!-- خانات اختيار الفروع والمادة المشتركة للمدير العام بتصميم كلاسيكي أكاديمي منظم -->
                    <div class="branch-selector-box" style="border-top: 3px solid #dc2626;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                            <label class="f-label" style="margin: 0; font-weight: 800; color: #991b1b; font-size: 0.9rem;">
                                <i class="fa-solid fa-layer-group" style="color: #dc2626;"></i> {{ __('الفروع الأكاديمية المستهدفة:') }}
                            </label>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" onclick="selectAllModalBranches('file', true)" class="btn-branch-util">{{ __('تحديد الكل') }}</button>
                                <button type="button" onclick="selectAllModalBranches('file', false)" class="btn-branch-util btn-branch-util-clear">{{ __('إلغاء التحديد') }}</button>
                            </div>
                        </div>

                        <div class="branch-cards-grid">
                            @foreach($stages as $stage)
                                @php
                                    $shortName = $stage->short_label ?? $stage->label_ar;
                                    $icon = $stage->icon ?? '📖';
                                @endphp
                                <label class="branch-select-card" id="f_stage_card_{{ $stage->id }}">
                                    <input type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" class="file-modal-stage-check" onchange="onFileModalSelectionChange()" checked>
                                    <div class="branch-card-content">
                                        <span class="branch-icon">{{ $icon }}</span>
                                        <span class="branch-name">{{ $shortName }}</span>
                                        <i class="fa-solid fa-circle-check branch-check-icon" style="color: #dc2626;"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- المادة المشتركة المختارة -->
                        <div style="margin-top: 14px;">
                            <label class="f-label" style="font-weight: 700; color: #991b1b; font-size: 0.88rem; margin-bottom: 6px;">
                                <i class="fa-solid fa-book-bookmark" style="color: #dc2626;"></i> {{ __('المادة الدراسية المشتركة / المبحث:') }}
                            </label>
                            <select id="file_admin_subject_select" class="f-control" required onchange="onFileModalSelectionChange()" style="min-height: 48px; line-height: 1.6;">
                                <option value="">{{ __('اختر المادة الدراسية (مثال: اللغة العربية، اللغة الإنجليزية...)...') }}</option>
                                @php
                                    $uniqueSubjects = collect($subjects)->unique('clean_name');
                                @endphp
                                @foreach($uniqueSubjects as $uSub)
                                    <option value="{{ $uSub->id }}" data-clean-name="{{ $uSub->clean_name }}" data-key="{{ $uSub->subject_key }}">
                                        {{ $uSub->clean_name }}
                                    </option>
                                @endforeach
                                <option disabled>────────── فروع المواد التفصيلية ──────────</option>
                                @foreach($subjects as $sub)
                                    @php
                                        $subStageName = $sub->stage ? ($sub->stage->short_label ?? $sub->stage->label_ar) : 'عام';
                                    @endphp
                                    <option value="{{ $sub->id }}" data-clean-name="{{ $sub->clean_name }}" data-key="{{ $sub->subject_key }}">
                                        {{ $sub->clean_name }} — {{ $subStageName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- تنبيه الفروع والمواد المستهدفة بالتوازي -->
                        <div id="filePublishTargetAlert" style="display: none; margin-top: 12px; padding: 12px 14px; background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 10px;">
                            <div style="color: #991b1b; font-size: 0.84rem; font-weight: 800; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-check-double" style="color: #dc2626;"></i>
                                <span>{{ __('سيتم نشر هذه الملزمة وتوفيرها للمدرسين والطلبة بالتوازي في:') }}</span>
                            </div>
                            <div id="fileSelectedSubjectsList" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                        </div>
                        <div id="fileHiddenSubjectIdsWrap"></div>
                    </div>
                @else
                    <!-- المادة الدراسية للمعلم -->
                    <div class="f-group">
                        <label class="f-label">{{ __('المادة الدراسية والمرحلة *') }}</label>
                        <select name="subject_id" required class="f-control">
                            <option value="">{{ __('اختر المادة الدراسية...') }}</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ (isset($subjectId) && $subjectId == $sub->id) ? 'selected' : '' }}>
                                    {{ $sub->name_ar ?? $sub->name }} {{ optional($sub->stage)->label_ar ? ' - (' . optional($sub->stage)->label_ar . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- عنوان الدوسية -->
                <div class="f-group">
                    <label class="f-label">{{ __('عنوان الدوسية / الملزمة التعليمية *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('مثال: دوسية الشامل في الرياضيات - الوحدة الأولى (الأسئلة الوزارية)') }}" class="f-control">
                </div>

                <!-- خيار توجيه الملف (غزة / الضفة / كلاهما) -->
                <div class="f-group">
                    <label class="f-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            <i class="fa-solid fa-map-location-dot" style="color: #dc2626;"></i>
                            {{ __('الفئة المستهدفة من الطلبة *') }}
                        </span>
                        <span style="font-size: 0.76rem; color: #64748b;">
                            {{ __('يحدد من تظهر له هذه الدوسية في حسابه') }}
                        </span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 4px;">
                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="gaza" style="display: none;" onchange="updateDocRegionSelect(this)">
                            <div class="doc-region-box" id="doc_card_gaza" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🌿</div>
                                <strong style="display: block; font-size: 0.85rem; color: #065f46;">{{ __('قطاع غزة') }}</strong>
                                <span style="font-size: 0.7rem; color: #64748b;">{{ __('لطلبة غزة فقط') }}</span>
                            </div>
                        </label>

                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="west_bank" style="display: none;" onchange="updateDocRegionSelect(this)">
                            <div class="doc-region-box" id="doc_card_west_bank" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🏛️</div>
                                <strong style="display: block; font-size: 0.85rem; color: #1e40af;">{{ __('الضفة والقدس') }}</strong>
                                <span style="font-size: 0.7rem; color: #64748b;">{{ __('لطلبة الضفة فقط') }}</span>
                            </div>
                        </label>

                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="all" checked style="display: none;" onchange="updateDocRegionSelect(this)">
                            <div class="doc-region-box active" id="doc_card_all" style="border: 2px solid #dc2626; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #fef2f2;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🌐</div>
                                <strong style="display: block; font-size: 0.85rem; color: #991b1b;">{{ __('منهاج مشترك') }}</strong>
                                <span style="font-size: 0.7rem; color: #ef4444;">{{ __('لكافة طلبة الوطن') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- تصنيف الملف -->
                <div class="f-row">
                    <div class="f-group" style="flex: 1;">
                        <label class="f-label">{{ __('نوع وتصنيف الملف') }}</label>
                        <select name="channel_name" class="f-control">
                            <option value="دوسية المنهاج الشاملة">{{ __('دوسية المنهاج الشاملة') }}</option>
                            <option value="ملخص وتلخيص وزاري">{{ __('ملخص وتلخيص وزاري') }}</option>
                            <option value="ورقة عمل وتدريبات">{{ __('ورقة عمل وتدريبات') }}</option>
                            <option value="بنك أسئلة وإجابات نموذجية">{{ __('بنك أسئلة وإجابات نموذجية') }}</option>
                            <option value="ملزمة امتحانات سابقة">{{ __('ملزمة امتحانات سابقة') }}</option>
                            <option value="كتيب مراجعة نهائية">{{ __('كتيب مراجعة نهائية') }}</option>
                        </select>
                    </div>

                    <div class="f-group" style="width: 130px;">
                        <label class="f-label">{{ __('ترتيب الملف') }}</label>
                        <input type="number" name="order" value="1" min="1" class="f-control font-mono">
                    </div>
                </div>

                <!-- رفع الملف المباشر -->
                <div class="f-group">
                    <label class="f-label">
                        <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                        <span>{{ __('اختيار ملف الدوسية / المستند *') }}</span>
                    </label>
                    <input type="file" name="file_upload_pdf" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg,.webp,.zip,.rar,.txt" class="f-control file-input">
                    <small class="f-hint">{{ __('يدعم ملفات PDF، Word، Excel، PowerPoint، والملفات المضغوطة ZIP حتى 100 ميجابايت.') }}</small>
                </div>

                <!-- أو رابط خارجي سحابي -->
                <div class="f-group">
                    <label class="f-label">
                        <i class="fa-solid fa-link" style="color: #64748b;"></i>
                        <span>{{ __('أو رابط سحابي مباشر (Google Drive / OneDrive / رابط مباشر)') }}</span>
                    </label>
                    <input type="url" name="pdf_url" placeholder="https://drive.google.com/..." class="f-control font-mono text-ltr">
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" onclick="closeUploadFileModal()" class="btn-modal-cancel">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSubmitDoc" class="btn-modal-submit-file">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('حفظ وتثبيت الملزمة للطلبة') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ==========================================================
   ACADEMIC DOSSIER & FILES MANAGEMENT STYLES
   ========================================================== */
.ed-teacher-files-container {
    width: 100%;
    margin: 0 auto;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-teacher-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 20px;
}

.ed-teacher-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #64748b;
    margin-bottom: 6px;
}

.ed-teacher-breadcrumbs a {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}

.ed-teacher-breadcrumbs a:hover {
    color: #dc2626;
}

.ed-teacher-breadcrumbs .active {
    color: #dc2626;
    font-weight: 800;
}

.ed-teacher-header h1 {
    margin: 0 0 6px;
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ed-teacher-header p {
    margin: 0;
    color: #64748b;
    font-size: 0.88rem;
    max-width: 780px;
    line-height: 1.6;
}

.ed-btn-upload-file {
    background: #dc2626;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
}

.ed-btn-upload-file:hover {
    background: #991b1b;
    transform: translateY(-1px);
}

/* Stats Strip */
.ed-stats-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.stat-num {
    display: block;
    font-size: 1.45rem;
    font-weight: 900;
    color: #0f172a;
    font-family: inherit;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
}

/* Filter Bar */
.ed-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-chip {
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.15s ease;
}

.filter-chip.active {
    background: #dc2626;
    color: #ffffff;
}

.filter-chip:hover:not(.active) {
    background: #e2e8f0;
    color: #0f172a;
}

/* Files Grid */
.ed-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 22px;
}

.ed-file-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ed-file-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

.file-card-header {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    margin-bottom: 16px;
}

.file-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

.file-tag {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 2px 8px;
    border-radius: 4px;
    margin-bottom: 4px;
}

.file-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1.45;
}

.file-meta-box {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.78rem;
    color: #475569;
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 16px;
}

.file-meta-box div {
    display: flex;
    align-items: center;
    gap: 8px;
}

.file-card-footer {
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.btn-download {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    transition: 0.15s;
}

.btn-download:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

.footer-control-side {
    display: flex;
    align-items: center;
    gap: 6px;
}

.visibility-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 0.85rem;
    display: grid;
    place-items: center;
    transition: 0.15s;
}

.visibility-btn.is-visible {
    background: #ecfdf5;
    color: #059669;
}

.visibility-btn.is-hidden {
    background: #fef2f2;
    color: #dc2626;
}

.action-btns {
    display: flex;
    gap: 6px;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #eff6ff;
    color: #1d4ed8;
    text-decoration: none;
    font-size: 0.85rem;
    transition: 0.15s;
    position: relative;
    z-index: 10;
    pointer-events: auto !important;
    cursor: pointer;
}

.btn-edit:hover {
    background: #dbeafe;
}

.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    background: #fef2f2;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.85rem;
    transition: 0.15s;
    position: relative;
    z-index: 10;
    pointer-events: auto !important;
}

.btn-delete:hover {
    background: #fee2e2;
}

/* Empty State */
.ed-empty-files {
    grid-column: 1 / -1;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 60px 20px;
    text-align: center;
}

.ed-empty-files i {
    font-size: 3rem;
    color: #94a3b8;
    margin-bottom: 12px;
}

.ed-empty-files h3 {
    font-size: 1.2rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}

.ed-empty-files p {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 20px;
}

/* ==========================================================
   CLASSIC ROYAL ACADEMIC UPLOAD MODAL (FILES & DOSSIERS)
   ========================================================== */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.72);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: none;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
    padding: 24px 16px;
    box-sizing: border-box;
}

.modal-card {
    background: #ffffff;
    border-radius: 18px;
    max-width: 720px;
    width: 100%;
    margin: 16px auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(226, 232, 240, 0.8);
    border: 1px solid #cbd5e1;
    border-top: 4px solid #dc2626;
    animation: modalScale 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 50px);
    overflow: hidden;
}

@keyframes modalScale {
    from { opacity: 0; transform: scale(0.96) translateY(6px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-head {
    flex-shrink: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    background: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
}

.modal-head h3 {
    margin: 0;
    font-size: 1.18rem;
    font-weight: 800;
    color: #991b1b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-close-modal {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 1.3rem;
    color: #64748b;
    cursor: pointer;
    line-height: 1;
    display: grid;
    place-items: center;
    transition: all 0.2s ease;
}

.btn-close-modal:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
    transform: rotate(90deg);
}

.modal-form-body {
    flex: 1 1 auto;
    overflow-y: auto;
    padding: 22px 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: 100%;
}

.modal-form-body::-webkit-scrollbar {
    width: 6px;
}
.modal-form-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.modal-form-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.modal-form-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.f-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.f-label {
    font-size: 0.86rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.f-control {
    width: 100%;
    min-height: 48px;
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    font-family: inherit;
    font-size: 0.92rem;
    line-height: 1.6;
    outline: none;
    background: #ffffff;
    color: #1e293b;
    box-sizing: border-box;
    transition: all 0.2s ease;
}

select.f-control {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 14px center;
    background-size: 16px;
    padding-left: 38px;
}

.f-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    background: #ffffff;
}

/* بطاقات اختيار الفروع الأكاديمية الأنيقة */
.branch-selector-box {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    box-sizing: border-box;
}

.btn-branch-util {
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 0.76rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-branch-util:hover {
    background: #cbd5e1;
    color: #0f172a;
}
.btn-branch-util-clear {
    background: #ffffff;
    color: #64748b;
}
.btn-branch-util-clear:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

.branch-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    margin-top: 8px;
}

.branch-select-card {
    position: relative;
    cursor: pointer;
    margin: 0;
    user-select: none;
    display: block;
}
.branch-select-card input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.branch-card-content {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    transition: all 0.2s ease;
}
.branch-select-card:hover .branch-card-content {
    border-color: #94a3b8;
    background: #f8fafc;
}
.branch-select-card input:checked ~ .branch-card-content {
    border-color: #dc2626;
    background: #fef2f2;
    color: #991b1b;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.12);
}
.branch-check-icon {
    margin-right: auto;
    font-size: 0.85rem;
    color: #cbd5e1;
    transition: all 0.2s;
}
.branch-select-card input:checked ~ .branch-card-content .branch-check-icon {
    color: #dc2626;
}

.f-hint {
    color: #64748b;
    font-size: 0.74rem;
}

.f-row {
    display: flex;
    gap: 12px;
}

.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
}

.btn-modal-cancel {
    padding: 10px 20px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    font-weight: 700;
    cursor: pointer;
}

.btn-modal-submit-file {
    padding: 10px 24px;
    border-radius: 8px;
    background: #dc2626;
    color: white;
    border: none;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.15s;
}

.btn-modal-submit-file:hover {
    background: #991b1b;
}

@media (max-width: 768px) {
    .ed-teacher-header {
        padding: 14px 16px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .header-actions {
        width: 100%;
    }
    .ed-btn-upload-file {
        width: 100%;
        justify-content: center;
        display: inline-flex;
    }
    .ed-stats-strip {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px;
    }
    .filter-pills {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
    }
    .filter-chip {
        white-space: nowrap;
        flex-shrink: 0;
    }
    .ed-files-grid {
        grid-template-columns: 1fr !important;
    }
    .modal-overlay {
        padding: 10px !important;
        align-items: flex-start !important;
    }
    .modal-card {
        width: 100% !important;
        max-width: 100% !important;
        max-height: calc(100dvh - 24px) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding: 18px 14px !important;
        border-radius: 14px !important;
        box-sizing: border-box !important;
    }
    .modal-foot {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .btn-modal-submit-file,
    .btn-modal-cancel {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .ed-stats-strip {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
const allFileModalSubjects = @json($subjects ?? []);
const isFileModalAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};

function selectAllModalBranches(prefix, checked) {
    document.querySelectorAll('.' + prefix + '-modal-stage-check').forEach(cb => {
        cb.checked = checked;
        const lbl = document.getElementById(prefix === 'video' ? 'v_stage_lbl_' + cb.value : 'f_stage_lbl_' + cb.value);
        if (lbl) {
            lbl.style.borderColor = checked ? '#dc2626' : '#cbd5e1';
            lbl.style.background = checked ? '#fef2f2' : '#ffffff';
        }
    });
    if (typeof onFileModalSelectionChange === 'function') onFileModalSelectionChange();
}

function onFileModalSelectionChange() {
    if (!isFileModalAdmin) return;
    const sel = document.getElementById('file_admin_subject_select');
    if (!sel) return;
    const selectedOpt = sel.options[sel.selectedIndex];
    const hiddenWrap = document.getElementById('fileHiddenSubjectIdsWrap');
    const alertBox = document.getElementById('filePublishTargetAlert');
    const listDiv = document.getElementById('fileSelectedSubjectsList');

    if (!hiddenWrap || !alertBox || !listDiv) return;

    hiddenWrap.innerHTML = '';
    listDiv.innerHTML = '';

    if (!selectedOpt || !selectedOpt.value) {
        alertBox.style.display = 'none';
        return;
    }

    const checkedStages = Array.from(document.querySelectorAll('.file-modal-stage-check:checked')).map(cb => parseInt(cb.value));
    const cleanName = (selectedOpt.getAttribute('data-clean-name') || '').trim();
    const primaryId = parseInt(selectedOpt.value);

    const matched = allFileModalSubjects.filter(sub => {
        if (!checkedStages.includes(parseInt(sub.stage_id))) return false;
        if (sub.id === primaryId) return true;
        if (cleanName && sub.name_ar && sub.name_ar.includes(cleanName)) return true;
        return false;
    });

    if (matched.length === 0) {
        const pSub = allFileModalSubjects.find(s => s.id === primaryId);
        if (pSub) matched.push(pSub);
    }

    matched.forEach(sub => {
        hiddenWrap.innerHTML += `<input type="hidden" name="subject_ids[]" value="${sub.id}">`;
        const branchLabel = (sub.stage && sub.stage.short_label) ? sub.stage.short_label : (sub.stage ? sub.stage.label_ar : 'الفرع الأكاديمي');
        const subjectClean = (sub.clean_name || sub.name_ar || '').replace(/\(.*?\)/g, '').trim();

        listDiv.innerHTML += `
            <div style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1.5px solid #fca5a5; border-radius: 8px; padding: 5px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <span style="background: #dc2626; color: #ffffff; font-size: 0.74rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">${branchLabel}</span>
                <span style="color: #1e293b; font-weight: 700; font-size: 0.86rem;">${subjectClean}</span>
            </div>
        `;
    });

    hiddenWrap.innerHTML += `<input type="hidden" name="subject_id" value="${matched[0].id}">`;
    alertBox.style.display = 'block';
}

function openUploadFileModal() {
    const m = document.getElementById('uploadFileModal');
    if (m) {
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeUploadFileModal() {
    const m = document.getElementById('uploadFileModal');
    if (m) {
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function updateDocRegionSelect(radio) {
    ['doc_card_gaza', 'doc_card_west_bank', 'doc_card_all'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.borderColor = '#e2e8f0';
            el.style.background = '#ffffff';
        }
    });
    if (radio.value === 'gaza') {
        const c = document.getElementById('doc_card_gaza');
        if (c) { c.style.borderColor = '#059669'; c.style.background = '#ecfdf5'; }
    } else if (radio.value === 'west_bank') {
        const c = document.getElementById('doc_card_west_bank');
        if (c) { c.style.borderColor = '#1e40af'; c.style.background = '#eff6ff'; }
    } else {
        const c = document.getElementById('doc_card_all');
        if (c) { c.style.borderColor = '#dc2626'; c.style.background = '#fef2f2'; }
    }
}

function closeUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'none';
}

async function submitDocForm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitDoc');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري رفع وتوثيق الملف...") }}';

    const form = document.getElementById('uploadDocForm');
    const formData = new FormData(form);

    const storeUrl = "{{ auth()->user()->role === 'admin' ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";

    try {
        const res = await axios.post(storeUrl, formData);
        Swal.fire({
            icon: 'success',
            title: res.data.title || '{{ __("تم حفظ ورفع الملزمة بنجاح 🎉") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#dc2626'
        }).then(() => location.reload());
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        const msg = err.response?.data?.title || err.response?.data?.message || '{{ __("حدث خطأ أثناء رفع الملف") }}';
        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
    }
}

async function toggleVisibility(id, btn) {
    if (!id) return;
    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const toggleUrl = (isUserAdmin ? '/admin/visibility/toggle/' : '/teacher/visibility/toggle/') + id;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    if (btn) btn.disabled = true;
    try {
        const res = await axios.post(toggleUrl, { _token: token });
        if (res.data && res.data.success) {
            const isVis = !!res.data.is_visible;
            if (btn) {
                btn.className = 'visibility-btn ' + (isVis ? 'is-visible' : 'is-hidden');
                btn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-eye-slash'}"></i>`;
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: res.data.message || 'تم تحديث حالة الظهور', timer: 1200, showConfirmButton: false });
            }
        } else {
            const errMsg = (res.data && res.data.error) ? res.data.error : '{{ __("تعذر تعديل حالة الظهور.") }}';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: errMsg });
            } else {
                alert(errMsg);
            }
        }
    } catch (e) {
        console.error('Toggle visibility error:', e);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("تعذر تعديل حالة الظهور.") }}' });
        } else {
            alert('تعذر تعديل حالة الظهور.');
        }
    } finally {
        if (btn) btn.disabled = false;
    }
}

window.deleteFileItem = async function(id, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    if (!id) return;

    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const deleteUrl = (isUserAdmin ? '/admin/educational-contents/' : '/teacher/educational_contents/') + id;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const cardEl = document.getElementById('file_card_' + id);

    const removeCardFromDom = () => {
        if (cardEl) {
            cardEl.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
            cardEl.style.opacity = '0';
            cardEl.style.transform = 'scale(0.9) translateY(10px)';
            setTimeout(() => {
                if (cardEl.parentNode) cardEl.parentNode.removeChild(cardEl);
                const grid = document.querySelector('.ed-files-grid');
                if (grid && grid.querySelectorAll('.ed-file-card').length === 0) {
                    location.reload();
                }
            }, 350);
        }
    };

    const executeDelete = async () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("جاري الحذف...") }}',
                text: '{{ __("يرجى الانتظار لحظات...") }}',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', token);

            const res = await axios.post(deleteUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const data = res.data;
            removeCardFromDom();

            const successMsg = data?.message || '{{ __("تم حذف الملف بنجاح.") }}';
            if (typeof Swal !== 'undefined') {
                await Swal.fire({ 
                    icon: 'success', 
                    title: '{{ __("تم الحذف بنجاح ✅") }}', 
                    text: successMsg,
                    timer: 1400, 
                    showConfirmButton: false 
                });
            }
        } catch (e) {
            console.warn("File delete error / 404:", e);
            const errMsg = e.response?.data?.message || e.message || '';

            // إذا كانت المحاضرة أو الملف محذوفاً مسبقاً، نزيله فوراً من الواجهة دون تركه عالقاً
            if (e.response?.status === 404 || errMsg.includes('غير موجود') || errMsg.includes('تم حذف') || errMsg.includes('already')) {
                removeCardFromDom();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon: 'success', 
                        title: '{{ __("تمت إزالة الملف ✅") }}', 
                        text: '{{ __("تم التأكد من مسح الملف من السيرفر وإزالته من القائمة.") }}',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
                return;
            }

            // محاولة الإرسال عبر النموذج الكلاسيكي كحل مضمون 100%
            const nativeForm = document.getElementById('delete_form_file_' + id);
            if (nativeForm) {
                nativeForm.submit();
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({ 
                    icon: 'error', 
                    title: '{{ __("تعذر إتمام الحذف") }}', 
                    text: errMsg || '{{ __("تعذر حذف الملف.") }}' 
                });
            } else {
                alert(errMsg || 'تعذر حذف الملف.');
            }
        }
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '{{ __("حذف هذه الملزمة؟") }}',
            text: '{{ __("هل أنت متأكد من حذف هذا الملف؟ لن يتمكن الطلاب من تحميله بعد الحذف.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("نعم، احذف") }}',
            cancelButtonText: '{{ __("إلغاء") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                executeDelete();
            }
        });
    } else {
        if (confirm('{{ __("هل أنت متأكد من حذف هذا الملف؟ لن يتمكن الطلاب من تحميله بعد الحذف.") }}')) {
            executeDelete();
        }
    }
};
function deleteFileItem(id, event) { return window.deleteFileItem(id, event); }

window.purgeAllContents = async function() {
    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const purgeUrl = isUserAdmin 
        ? '/admin/educational-contents/purge-all' 
        : '/teacher/educational-contents/purge-all';
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    const executePurge = async () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'جاري الحذف والتصفير الشامل...',
                text: 'يرجى الانتظار لحظات حتى إتمام مسح السجلات بالكامل...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        try {
            const formData = new FormData();
            formData.append('_token', token);
            formData.append('_method', 'DELETE');

            const res = await axios.post(purgeUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });

            const data = res.data;
            if (data && (data.success || res.status === 200)) {
                if (typeof Swal !== 'undefined') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'تم التصفير الشامل بنجاح! 🗑️',
                        text: data.message || 'تم حذف وتصفير جميع المحتويات من المنصة بالكامل.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert(data.message || 'تم التصفير بنجاح');
                }
                location.reload();
            } else {
                const errMsg = data?.message || 'تعذر استكمال عملية التصفير.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'خطأ', text: errMsg });
                } else {
                    alert('خطأ: ' + errMsg);
                }
            }
        } catch (e) {
            console.error("Purge error:", e);
            const errMsg = e.response?.data?.message || 'حدث خطأ في الاتصال أثناء التصفير.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'خطأ', text: errMsg });
            } else {
                alert('حدث خطأ في الاتصال أثناء التصفير.');
            }
        }
    };

    const confirmMsg = isUserAdmin
        ? 'هل أنت متأكد تماماً من رغبتك بحذف وتصفير كافة المحاضرات والمواد والملفات من المنصة بالكامل؟ لن يتمكن أي طالب أو معلم من الوصول إليها بعد ذلك!'
        : 'هل أنت متأكد تماماً من رغبتك بحذف وتصفير كافة المواد والملازم والدوسيات الخاصة بك؟ هذا الإجراء لا يمكن التراجع عنه!';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '⚠️ تحذير: تصفير وحذف كافة المحتويات دفعة واحدة',
            text: confirmMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، حذف وتصفير الكل فوراً',
            cancelButtonText: 'إلغاء التراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                executePurge();
            }
        });
    } else {
        if (confirm(confirmMsg)) {
            executePurge();
        }
    }
};
</script>
@endsection
