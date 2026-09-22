@extends('layouts.app')

@section('title', __('الاستفسارات والشكاوى الأكاديمية') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="inquiries-page-wrapper">

    {{-- رأس الصفحة الكلاسيكي الأكاديمي --}}
    <div class="inquiries-header-card">
        <div>
            <div class="academic-badge-tag">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('الدعم الأكاديمي والوزاري') }}</span>
            </div>
            <h1 class="inquiries-title">{{ __('الاستفسارات الأكاديمية وشكاوى الطلبة') }}</h1>
            <p class="inquiries-subtitle">{{ __('متابعة تذاكر واستفسارات طلبة التوجيهي وأولياء الأمور والمعلمين، والرد عليها فورياً.') }}</p>
        </div>
        <div class="header-action-tools">
            <a href="{{ route('admin.dashboard') }}" class="btn-classic-nav">
                <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('لوحة الإدارة') }}
            </a>
        </div>
    </div>

    {{-- بطاقات الإحصائيات الأكاديمية الكلاسيكية --}}
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('إجمالي التذاكر المسجلة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ $stats['total'] }}</span>
                <i class="fa-solid fa-inbox stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('بانتظار المعالجة والرد') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number {{ $stats['pending'] > 0 ? 'text-amber' : '' }}">{{ $stats['pending'] }}</span>
                <i class="fa-solid fa-clock-rotate-left stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">{{ __('تم الرد عليها بنجاح') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $stats['replied'] }}</span>
                <i class="fa-solid fa-circle-check stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('استفسارات المواد والدروس') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ $stats['academics'] }}</span>
                <i class="fa-solid fa-book-bookmark stat-icon text-indigo"></i>
            </div>
        </div>
    </div>

    {{-- شريط التصفية والبحث --}}
    <div class="filters-card-wrapper">
        <form method="GET" action="{{ route('admin.inquiries.index') }}" class="filters-action-form">
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث باسم الطالب، البريد، أو نص الرسالة...') }}" class="search-input-field">
            </div>
            
            <div class="select-field-wrap">
                <select name="category" onchange="this.form.submit()" class="filter-select-field">
                    <option value="all">{{ __('كافة التصنيفات') }}</option>
                    <option value="استفسار أكاديمي عن المساقات" {{ request('category') == 'استفسار أكاديمي عن المساقات' ? 'selected' : '' }}>📚 {{ __('استفسار أكاديمي عن المساقات') }}</option>
                    <option value="مشكلة فنية أو تقنية في المنصة" {{ request('category') == 'مشكلة فنية أو تقنية في المنصة' ? 'selected' : '' }}>⚙️ {{ __('مشكلة تقنية') }}</option>
                    <option value="طلب تفعيل حساب أو اشتراك" {{ request('category') == 'طلب تفعيل حساب أو اشتراك' ? 'selected' : '' }}>💳 {{ __('تفعيل حساب / اشتراك') }}</option>
                    <option value="اقتراح تطويري للمنصة" {{ request('category') == 'اقتراح تطويري للمنصة' ? 'selected' : '' }}>💡 {{ __('اقتراح تطويري') }}</option>
                    <option value="شكوى خاصة أخرى" {{ request('category') == 'شكوى خاصة أخرى' ? 'selected' : '' }}>📌 {{ __('أخرى') }}</option>
                </select>
            </div>

            <div class="select-field-wrap">
                <select name="status" onchange="this.form.submit()" class="filter-select-field">
                    <option value="all">{{ __('كافة الحالات') }}</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>🔴 {{ __('جديدة (قيد الانتظار)') }}</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>🟢 {{ __('تم الرد') }}</option>
                </select>
            </div>

            <button type="submit" class="btn-filter-apply">
                <i class="fa-solid fa-filter"></i> {{ __('تصفية') }}
            </button>

            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.inquiries.index') }}" class="btn-reset-filter">
                    {{ __('إلغاء الفلتر') }}
                </a>
            @endif
        </form>
    </div>

    {{-- جدول التذاكر والاستفسارات --}}
    <div class="inquiries-table-card">
        <div class="table-responsive-box">
            <table class="academic-table">
                <thead>
                    <tr>
                        <th>{{ __('المُرسل') }}</th>
                        <th>{{ __('التصنيف والموضوع') }}</th>
                        <th>{{ __('مقتطف الرسالة') }}</th>
                        <th>{{ __('الحالة') }}</th>
                        <th>{{ __('التاريخ') }}</th>
                        <th style="text-align: center;">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inq)
                        <tr id="inquiry-row-{{ $inq->id }}" class="{{ request('open_id') == $inq->id ? 'highlighted-inquiry-row' : '' }}">
                            <td>
                                <div class="sender-name">{{ $inq->name }}</div>
                                <div class="sender-email font-mono">{{ $inq->email }}</div>
                                @if($inq->phone)
                                    <div class="sender-phone font-mono">{{ $inq->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="category-badge">
                                    {{ $inq->category ? __($inq->category) : ($inq->type ? __($inq->type) : __('استفسار عام')) }}
                                </span>
                                <div class="subject-title">{{ $inq->subject ?? __('بدون عنوان') }}</div>
                            </td>
                            <td class="message-preview-cell">
                                <div class="message-preview-text">
                                    {{ $inq->message }}
                                </div>
                                @if($inq->reply)
                                    <small class="reply-sent-indicator">
                                        <i class="fa-solid fa-check-double"></i> {{ __('تم إرسال رد الإدارة') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($inq->status === 'replied')
                                    <span class="status-badge-replied">
                                        <span class="dot-green"></span> {{ __('تم الرد') }}
                                    </span>
                                @else
                                    <span class="status-badge-pending">
                                        <span class="dot-red"></span> {{ __('جديد (بانتظار الرد)') }}
                                    </span>
                                @endif
                            </td>
                            <td class="date-cell">
                                {{ $inq->created_at ? $inq->created_at->diffForHumans() : '' }}
                            </td>
                            <td style="text-align: center;">
                                <div class="action-buttons-group">
                                    <button type="button" id="btn-reply-{{ $inq->id }}" onclick="openReplyModal({{ json_encode($inq) }})" class="btn-reply-action" title="{{ __('قراءة التذكرة والرد') }}">
                                        <i class="fa-solid fa-reply"></i> {{ __('رد') }}
                                    </button>

                                    @if($inq->phone)
                                        @php
                                            $wa = preg_replace('/[^0-9]/', '', $inq->phone);
                                            if (str_starts_with($wa, '05')) $wa = '970' . substr($wa, 1);
                                        @endphp
                                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode(__('أهلاً بك أ. :name، بخصوص استفسارك في منارة التوجيهي:', ['name' => $inq->name])) }}" target="_blank" class="btn-whatsapp-action" title="{{ __('مراسلة سريعة عبر واتساب') }}">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذه التذكرة؟') }}');" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-action" title="{{ __('حذف') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-inquiries-state">
                                <i class="fa-solid fa-inbox empty-icon"></i>
                                <h4 class="empty-title">{{ __('لا توجد استفسارات أو شكاوى مطابقة') }}</h4>
                                <p class="empty-desc">{{ __('كافة استفسارات الطلبة والزوار تم الرد عليها بنجاح.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="pagination-footer-box">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>

{{-- نافذة استعراض التذكرة والرد الكلاسيكية --}}
<div id="replyModalOverlay" class="inquiry-modal-overlay" style="display: none;">
    <div class="inquiry-modal-card">
        <div class="inquiry-modal-header">
            <div>
                <h3 class="modal-main-title">{{ __('تفاصيل الاستفسار والرد الأكاديمي') }}</h3>
                <span id="modalInqSender" class="modal-sender-sub"></span>
            </div>
            <button type="button" onclick="closeReplyModal()" class="modal-btn-close">&times;</button>
        </div>

        <div class="inquiry-modal-body">
            <!-- نص رسالة الطالب -->
            <div class="student-message-preview-box">
                <div class="message-meta-row">
                    <span id="modalInqCategory" class="message-category-tag"></span>
                    <span id="modalInqDate" class="message-date-tag"></span>
                </div>
                <h4 id="modalInqSubject" class="message-subject-tag"></h4>
                <p id="modalInqMessage" class="message-body-tag"></p>
            </div>

            <form id="replyForm" onsubmit="submitReply(event)">
                <input type="hidden" id="modalInqId" value="">

                <div class="form-group-block">
                    <label class="field-label-bold">
                        {{ __('رد الإدارة / المشرف الأكاديمي') }} <span class="required-star">*</span>
                    </label>
                    <textarea id="modalReplyText" rows="4" placeholder="{{ __('اكتب ردك الوافي على استفسار الطالب هنا...') }}" class="textarea-academic-input" required></textarea>
                </div>

                <div class="form-group-block">
                    <label class="field-label-muted">
                        {{ __('ملاحظات إدارية داخلية (اختياري - للإدارة فقط):') }}
                    </label>
                    <input type="text" id="modalAdminNotes" placeholder="{{ __('مثال: تم التواصل هاتفياً وحل الإشكالية') }}" class="text-academic-input">
                </div>

                <div class="modal-actions-row">
                    <button type="button" onclick="closeReplyModal()" class="btn-cancel-modal">
                        {{ __('إلغاء') }}
                    </button>
                    <button type="submit" id="btnSubmitReply" class="btn-submit-modal">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ __('حفظ الرد واعتماده') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openReplyModal(inq) {
        document.getElementById('modalInqId').value = inq.id;
        document.getElementById('modalInqSender').innerText = inq.name + ' (' + inq.email + ')';
        document.getElementById('modalInqCategory').innerText = inq.category || '{{ __('استفسار عام') }}';
        document.getElementById('modalInqDate').innerText = inq.created_at ? inq.created_at.substring(0, 10) : '';
        document.getElementById('modalInqSubject').innerText = inq.subject || '{{ __('بدون عنوان') }}';
        document.getElementById('modalInqMessage').innerText = inq.message;
        document.getElementById('modalReplyText').value = inq.reply || '';
        document.getElementById('modalAdminNotes').value = inq.admin_notes || '';

        document.getElementById('replyModalOverlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReplyModal() {
        document.getElementById('replyModalOverlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    function submitReply(e) {
        e.preventDefault();
        const id = document.getElementById('modalInqId').value;
        const reply = document.getElementById('modalReplyText').value.trim();
        const notes = document.getElementById('modalAdminNotes').value.trim();

        const btn = document.getElementById('btnSubmitReply');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

        axios.post(`{{ url('admin/academic-inquiries') }}/${id}/reply`, {
            reply: reply,
            admin_notes: notes,
            _token: '{{ csrf_token() }}'
        })
        .then(res => {
            Swal.fire({
                icon: 'success',
                title: res.data.title,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>{{ __('حفظ الرد واعتماده') }}</span>';
            Swal.fire('{{ __('خطأ') }}', err.response?.data?.message || '{{ __('فشل حفظ الرد.') }}', 'error');
        });
    }

    window.onclick = function(e) {
        const modal = document.getElementById('replyModalOverlay');
        if (e.target === modal) closeReplyModal();
    }

    // فتح التذكرة المحددة تلقائياً عند النقر على إشعار من الإدارة
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const openId = urlParams.get('open_id');
        if (openId) {
            const targetRow = document.getElementById('inquiry-row-' + openId);
            const targetBtn = document.getElementById('btn-reply-' + openId);
            if (targetRow) {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if (targetBtn) {
                setTimeout(() => {
                    targetBtn.click();
                }, 350);
            }
        }
    });
</script>

<style>
    .highlighted-inquiry-row {
        background: #fef9c3 !important;
        outline: 2px solid #f59e0b;
        animation: pulseHighlight 2s ease;
    }
    @keyframes pulseHighlight {
        0% { background: #fef08a !important; }
        50% { background: #fef9c3 !important; }
        100% { background: #fef9c3 !important; }
    }
    .inquiries-page-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
    }

    .inquiries-header-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
        border-inline-start: 5px solid var(--ed-primary, #1e3a8a);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .academic-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .inquiries-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .inquiries-subtitle {
        color: #64748b;
        font-size: 0.88rem;
        margin: 0;
    }
    .btn-classic-nav {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 9px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
    }
    .btn-classic-nav:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    /* KPI Grid */
    .stats-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .stat-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .stat-icon-box.blue { background: #eff6ff; color: #1d4ed8; }
    .stat-icon-box.amber { background: #fffbeb; color: #d97706; }
    .stat-icon-box.emerald { background: #ecfdf5; color: #059669; }
    .stat-icon-box.navy { background: #f1f5f9; color: #1e3a8a; }
    .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 600; display: block; margin-bottom: 2px; }
    .stat-number { font-size: 1.45rem; font-weight: 700; color: #0f172a; }
    .text-amber { color: #d97706 !important; }
    .text-emerald { color: #059669 !important; }
    .text-navy { color: #1e3a8a !important; }

    /* Filters */
    .filters-card-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }
    .filters-action-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-input-wrap {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    .search-icon {
        position: absolute;
        inset-inline-start: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.85rem;
    }
    .search-input-field {
        width: 100%;
        padding: 9px 12px 9px 36px;
        padding-inline-start: 36px;
        padding-inline-end: 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        outline: none;
        box-sizing: border-box;
        background: #f8fafc;
    }
    .search-input-field:focus {
        border-color: #1e3a8a;
        background: #ffffff;
    }
    .filter-select-field {
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        outline: none;
        background: #ffffff;
        color: #0f172a;
    }
    .btn-filter-apply {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        padding: 9px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter-apply:hover { background: #172554; }
    .btn-reset-filter {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: underline;
        margin-inline-start: 4px;
    }

    /* Table */
    .inquiries-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .table-responsive-box { overflow-x: auto; }
    .academic-table {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
        font-size: 0.88rem;
    }
    .academic-table thead th {
        background: #f8fafc !important;
        border-bottom: 2px solid #cbd5e1 !important;
        color: #0f172a !important;
        font-size: 0.82rem;
        font-weight: 800;
        padding: 12px 16px;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .academic-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .academic-table tbody tr:hover { background: #f8fafc; }
    .academic-table td { padding: 14px 18px; vertical-align: middle; }

    .sender-name { font-weight: 700; color: #0f172a; margin-bottom: 2px; }
    .sender-email { font-size: 0.78rem; color: #64748b; }
    .sender-phone { font-size: 0.78rem; color: #059669; font-weight: 600; }

    .category-badge {
        background: #eff6ff;
        color: #1e40af;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 4px;
    }
    .subject-title { font-size: 0.85rem; font-weight: 600; color: #1e293b; }

    .message-preview-cell { max-width: 340px; }
    .message-preview-text {
        color: #475569;
        font-size: 0.84rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .reply-sent-indicator {
        color: #059669;
        font-size: 0.75rem;
        display: block;
        margin-top: 3px;
        font-weight: 600;
    }

    .status-badge-replied {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dot-green { width: 7px; height: 7px; border-radius: 50%; background: #10b981; }

    .status-badge-pending {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .dot-red { width: 7px; height: 7px; border-radius: 50%; background: #ef4444; }

    .date-cell { font-size: 0.78rem; color: #64748b; white-space: nowrap; }

    .action-buttons-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-reply-action {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-reply-action:hover { background: #172554; }
    .btn-whatsapp-action {
        background: #22c55e;
        color: #ffffff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-delete-action {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        cursor: pointer;
    }

    .empty-inquiries-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-icon { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.5; }
    .empty-title { font-size: 1.05rem; color: #475569; margin: 0 0 6px; }
    .empty-desc { font-size: 0.82rem; margin: 0; }

    .pagination-footer-box {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
    }

    /* Modal */
    .inquiry-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .inquiry-modal-card {
        background: #ffffff;
        border-radius: 14px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .inquiry-modal-header {
        padding: 16px 22px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-main-title { margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a; }
    .modal-sender-sub { font-size: 0.78rem; color: #64748b; }
    .modal-btn-close {
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #94a3b8;
        cursor: pointer;
    }
    .inquiry-modal-body {
        padding: 22px;
        max-height: 75vh;
        overflow-y: auto;
    }
    .student-message-preview-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 18px;
    }
    .message-meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .message-category-tag { font-size: 0.76rem; font-weight: 700; color: #1e40af; }
    .message-date-tag { font-size: 0.72rem; color: #94a3b8; }
    .message-subject-tag { margin: 0 0 6px; font-size: 0.9rem; color: #0f172a; font-weight: 700; }
    .message-body-tag { margin: 0; font-size: 0.85rem; color: #334155; line-height: 1.6; white-space: pre-wrap; }

    .form-group-block { margin-bottom: 16px; }
    .field-label-bold {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .field-label-muted {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
    }
    .required-star { color: #ef4444; }
    .textarea-academic-input {
        width: 100%;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.88rem;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
    }
    .textarea-academic-input:focus { border-color: #1e3a8a; }
    .text-academic-input {
        width: 100%;
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 0.85rem;
        font-family: inherit;
        outline: none;
        box-sizing: border-box;
    }
    .modal-actions-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }
    .btn-cancel-modal {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
    }
    .btn-submit-modal {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
        padding: 9px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-submit-modal:hover { background: #172554; }
</style>
@endsection
