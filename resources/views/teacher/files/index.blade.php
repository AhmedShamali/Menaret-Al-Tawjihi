@extends('layouts.app')

@section('title', __('إدارة ورفع الملازم والدوسيات والملفات') . ' | ' . config('app.name', 'منارة التوجيهي'))

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

        <div class="header-actions">
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

    <!-- شريط التصفية حسب المادة -->
    @if(auth()->user()->role === 'admin' || !auth()->user()->subject_id)
        <div class="ed-filter-bar">
            <div class="filter-label">
                <i class="fa-solid fa-filter"></i>
                <span>{{ __('تصفية حسب المادة الدراسية:') }}</span>
            </div>
            <div class="filter-pills">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files') }}" class="filter-chip {{ empty(request('subject_id')) ? 'active' : '' }}">
                    {{ __('جميع المواد') }}
                </a>
                @foreach($subjects as $sub)
                    <a href="{{ (auth()->user()->role === 'admin' ? route('admin.files') : route('teacher.files')) . '?subject_id=' . $sub->id }}" class="filter-chip {{ request('subject_id') == $sub->id ? 'active' : '' }}">
                        {{ $sub->name_ar ?? $sub->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

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
            <div class="ed-file-card">
                <div>
                    <div class="file-card-header">
                        <div class="file-icon-box" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }};">
                            <i class="{{ $meta['icon'] }}"></i>
                        </div>
                        <div style="overflow: hidden; flex: 1;">
                            <span class="file-tag">
                                {{ $file->subject?->name_ar ?? __('عام') }} • {{ $meta['label'] }}
                            </span>
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

                            <button type="button" onclick="deleteFileItem({{ $file->id }})" class="btn-delete" title="{{ __('حذف') }}">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
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
                <!-- المادة الدراسية -->
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

                <!-- عنوان الدوسية -->
                <div class="f-group">
                    <label class="f-label">{{ __('عنوان الدوسية / الملزمة التعليمية *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('مثال: دوسية الشامل في الرياضيات - الوحدة الأولى (الأسئلة الوزارية)') }}" class="f-control">
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

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-card {
    background: #ffffff;
    border-radius: 16px;
    max-width: 580px;
    width: 100%;
    padding: 26px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    animation: modalScale 0.2s ease-out;
}

@keyframes modalScale {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
}

.modal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 14px;
    margin-bottom: 18px;
}

.modal-head h3 {
    margin: 0;
    font-size: 1.18rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
}

.modal-form-body {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.f-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.f-label {
    font-size: 0.82rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.f-control {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit;
    font-size: 0.88rem;
    outline: none;
    background: #ffffff;
    color: #0f172a;
    box-sizing: border-box;
    transition: border-color 0.15s;
}

.f-control:focus {
    border-color: #dc2626;
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
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function openUploadFileModal() {
    document.getElementById('uploadFileModal').style.display = 'flex';
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
    const toggleUrl = "{{ auth()->user()->role === 'admin' ? url('admin/visibility/toggle') : url('teacher/visibility/toggle') }}/" + id;
    try {
        const res = await axios.post(toggleUrl, { _token: '{{ csrf_token() }}' });
        if (res.data.success) {
            const isVis = res.data.is_visible;
            btn.className = 'visibility-btn ' + (isVis ? 'is-visible' : 'is-hidden');
            btn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-eye-slash'}"></i>`;
            Swal.fire({ icon: 'success', title: res.data.message, timer: 1000, showConfirmButton: false });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("تعذر تعديل حالة الظهور.") }}' });
    }
}

async function deleteFileItem(id) {
    const deleteUrl = "{{ auth()->user()->role === 'admin' ? url('admin/educational-contents') : url('teacher/educational_contents') }}/" + id;
    Swal.fire({
        title: '{{ __("حذف هذه الملزمة؟") }}',
        text: '{{ __("هل أنت متأكد من حذف هذا الملف؟ لن يتمكن الطلاب من تحميله بعد الحذف.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("نعم، احذف") }}',
        cancelButtonText: '{{ __("إلغاء") }}'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(deleteUrl, {
                    data: { _token: '{{ csrf_token() }}' }
                });
                Swal.fire({ icon: 'success', title: '{{ __("تم حذف الملف بنجاح") }}', timer: 1200, showConfirmButton: false })
                    .then(() => location.reload());
            } catch (e) {
                Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: '{{ __("تعذر حذف الملف.") }}' });
            }
        }
    });
}
</script>
@endsection
