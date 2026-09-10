@extends('layouts.app')

@section('title', 'مركز الإشعارات والتنبيهات الشامل 🔔')

@section('content')
<div class="notifications-page-container" dir="rtl">

    <!-- كرت الترويسة الرئيسية -->
    <div class="notif-hero-card">
        <div class="hero-content">
            <div class="badge-tag">
                <i class="fa-solid fa-bell-ring"></i> مركز الإشعارات الفورية
            </div>
            <h1 class="hero-title">تنبيهاتك الأكاديمية والمالية 🇵🇸</h1>
            <p class="hero-subtitle">
                تابع ردود معلمي الثانوية العامة، تنبيهات تفعيل المواد، ومواعيد الامتحانات الوزارية أولاً بأول.
            </p>
        </div>

        <div class="hero-actions">
            @if($counts['unread'] > 0)
                <button type="button" class="btn-mark-all" onclick="markAllNotificationsRead()">
                    <i class="fa-solid fa-check-double"></i>
                    <span>تحديد الكل كمقروء ({{ $counts['unread'] }})</span>
                </button>
            @else
                <div class="all-read-badge">
                    <i class="fa-solid fa-circle-check text-success"></i>
                    <span>جميع التنبيهات مقروءة</span>
                </div>
            @endif
        </div>
    </div>

    <!-- شريط التبويبات والتصنيفات (Filter Pills) -->
    <div class="filter-tabs-wrapper">
        <div class="filter-tabs-scroll">
            <a href="{{ route('student.notifications.index', ['filter' => 'all']) }}" 
               class="filter-pill {{ $filter === 'all' ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>كافة الإشعارات</span>
                <span class="pill-count">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'unread']) }}" 
               class="filter-pill {{ $filter === 'unread' ? 'active' : '' }}">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>غير المقروءة</span>
                @if($counts['unread'] > 0)
                    <span class="pill-count unread-badge">{{ $counts['unread'] }}</span>
                @else
                    <span class="pill-count">0</span>
                @endif
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'message']) }}" 
               class="filter-pill {{ $filter === 'message' ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i>
                <span>المراسلات والمعلمين</span>
                <span class="pill-count">{{ $counts['message'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'payment']) }}" 
               class="filter-pill {{ $filter === 'payment' ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>الاشتراكات والمدفوعات</span>
                <span class="pill-count">{{ $counts['payment'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'exam']) }}" 
               class="filter-pill {{ $filter === 'exam' ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>الامتحانات والنتائج</span>
                <span class="pill-count">{{ $counts['exam'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'academic']) }}" 
               class="filter-pill {{ $filter === 'academic' ? 'active' : '' }}">
                <i class="fa-solid fa-fire"></i>
                <span>الإنجازات والتقدم</span>
                <span class="pill-count">{{ $counts['academic'] }}</span>
            </a>
        </div>
    </div>

    <!-- قائمة بطاقات الإشعارات -->
    <div class="notifications-feed-lane">
        @forelse($items as $notif)
            @php
                $isUnread = !$notif->is_read;
                $type = $notif->type ?? 'system';
                
                // تحديد الأيقونة والتدرج اللوني بحسب نوع الإشعار
                $iconClass = match($type) {
                    'payment'  => 'fa-solid fa-credit-card',
                    'message'  => 'fa-solid fa-comment-dots',
                    'exam'     => 'fa-solid fa-file-pen',
                    'streak'   => 'fa-solid fa-fire-flame-curved',
                    'academic' => 'fa-solid fa-graduation-cap',
                    default    => 'fa-solid fa-bell'
                };
                
                $iconTypeClass = match($type) {
                    'payment'  => 'type-payment',
                    'message'  => 'type-message',
                    'exam'     => 'type-exam',
                    'streak'   => 'type-streak',
                    'academic' => 'type-academic',
                    default    => 'type-system'
                };
            @endphp

            <div class="notif-feed-card {{ $isUnread ? 'is-unread' : '' }}" 
                 id="card_{{ $notif->id }}" 
                 onclick="window.location.href='{{ route('notifications.open', $notif->id) }}'" 
                 style="cursor: pointer;">
                <div class="notif-card-start">
                    <div class="notif-icon-avatar {{ $iconTypeClass }}">
                        <i class="{{ $iconClass }}"></i>
                    </div>

                    <div class="notif-body-content">
                        <div class="notif-headline-row">
                            <h3 class="notif-headline">{{ $notif->title }}</h3>
                            @if($isUnread)
                                <span class="badge-new-dot">جديد</span>
                            @endif
                        </div>

                        <p class="notif-text-message">
                            {{ $notif->message }}
                        </p>

                        <div class="notif-meta-tags">
                            <span class="meta-time">
                                <i class="fa-regular fa-clock"></i>
                                {{ $notif->created_at ? \Carbon\Carbon::parse($notif->created_at)->diffForHumans() : 'الآن' }}
                            </span>
                            <span class="meta-category-tag">
                                {{ match($type) {
                                    'payment' => 'معاملة مالية',
                                    'message' => 'رسالة خاصة',
                                    'exam' => 'اختبار وزاري',
                                    'streak' => 'حماس ودراسة',
                                    default => 'تنبيه نظام'
                                } }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء السريع -->
                <div class="notif-card-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('notifications.open', $notif->id) }}" 
                       class="btn-open-action" 
                       title="الانتقال للرابط المطلوب">
                        <span>فتح</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>

                    <div class="action-mini-group">
                        @if($isUnread)
                            <button type="button" 
                                    class="btn-icon-subtle" 
                                    onclick="event.stopPropagation(); markReadDirect('{{ $notif->id }}')" 
                                    title="تعيين كمقروء">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endif

                        <button type="button" 
                                class="btn-icon-subtle delete-btn" 
                                onclick="event.stopPropagation(); deleteNotifItem('{{ $notif->id }}')" 
                                title="حذف من السجل">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-feed-card">
                <div class="empty-icon-circle">
                    <i class="fa-solid fa-bell-slash"></i>
                </div>
                <h3>لا توجد إشعارات في هذا التصنيف</h3>
                <p>كل التنبيهات والردود والأنشطة الأكاديمية الجديدة ستظهر لك هنا مباشرة وبصورة فورية.</p>
                <a href="{{ route('student.courses.catalog') }}" class="btn-explore-empty">
                    <i class="fa-solid fa-compass"></i> استكشاف المواد والامتحانات
                </a>
            </div>
        @endforelse
    </div>

    <!-- ترقيم الصفحات (Pagination) -->
    @if($lastPage > 1)
        <div class="pagination-container">
            @for($i = 1; $i <= $lastPage; $i++)
                <a href="{{ route('student.notifications.index', ['filter' => $filter, 'page' => $i]) }}" 
                   class="page-link-pill {{ $page === $i ? 'active' : '' }}">
                    {{ $i }}
                </a>
            @endfor
        </div>
    @endif

</div>

<style>
:root {
    --max-w: 920px;
}

.notifications-page-container {
    max-width: var(--max-w);
    margin: 1.5rem auto 3rem;
    padding: 0 1rem;
}

/* 1. كرت الترويسة */
.notif-hero-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 24px 30px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0,0,0,0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 22px;
}

body.dark-theme .notif-hero-card {
    background: #0f172a;
    border-color: #1e293b;
}

.badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #0284c7;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.hero-title {
    font-size: 1.4rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 4px;
}

body.dark-theme .hero-title { color: #f8fafc; }

.hero-subtitle {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
}

.btn-mark-all {
    background: #0284c7;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    transition: 0.2s;
}

.btn-mark-all:hover {
    background: #0369a1;
    transform: translateY(-1px);
}

.all-read-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
}

/* 2. شريط التبويبات (Tabs) */
.filter-tabs-wrapper {
    margin-bottom: 20px;
    overflow-x: auto;
    scrollbar-width: none;
}

.filter-tabs-wrapper::-webkit-scrollbar { display: none; }

.filter-tabs-scroll {
    display: flex;
    gap: 8px;
    padding-bottom: 4px;
}

.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
    color: #475569;
    text-decoration: none;
    font-size: 0.84rem;
    font-weight: 700;
    white-space: nowrap;
    transition: all 0.2s;
}

body.dark-theme .filter-pill {
    background: #0f172a;
    border-color: #1e293b;
    color: #94a3b8;
}

.filter-pill:hover {
    border-color: #0284c7;
    color: #0284c7;
}

.filter-pill.active {
    background: #0284c7;
    border-color: #0284c7;
    color: white;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
}

.pill-count {
    background: rgba(0,0,0,0.06);
    padding: 1px 7px;
    border-radius: 20px;
    font-size: 0.72rem;
}

.filter-pill.active .pill-count {
    background: rgba(255,255,255,0.25);
    color: white;
}

.unread-badge {
    background: #ef4444 !important;
    color: white !important;
}

/* 3. بطاقات الإشعارات */
.notifications-feed-lane {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notif-feed-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    transition: all 0.2s ease;
}

body.dark-theme .notif-feed-card {
    background: #0f172a;
    border-color: #1e293b;
}

.notif-feed-card:hover {
    border-color: #0284c7;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.04);
}

.notif-feed-card.is-unread {
    background: #f0fdf4;
    border-color: #bbf7d0;
    border-right: 4px solid #10b981;
}

body.dark-theme .notif-feed-card.is-unread {
    background: #064e3b20;
    border-color: #065f46;
    border-right-color: #10b981;
}

.notif-card-start {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
}

.notif-icon-avatar {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.type-payment { background: #dcfce7; color: #059669; }
.type-message { background: #e0f2fe; color: #0284c7; }
.type-exam    { background: #fef3c7; color: #d97706; }
.type-streak  { background: #ffedd5; color: #ea580c; }
.type-academic{ background: #ede9fe; color: #7c3aed; }
.type-system  { background: #f1f5f9; color: #475569; }

.notif-body-content {
    flex: 1;
}

.notif-headline-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.notif-headline {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
}

body.dark-theme .notif-headline { color: #f8fafc; }

.badge-new-dot {
    background: #ef4444;
    color: white;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 1px 7px;
    border-radius: 12px;
}

.notif-text-message {
    color: #475569;
    font-size: 0.88rem;
    line-height: 1.55;
    margin: 0 0 8px;
}

body.dark-theme .notif-text-message { color: #cbd5e1; }

.notif-meta-tags {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.75rem;
    color: #94a3b8;
}

.meta-category-tag {
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 8px;
    color: #64748b;
    font-weight: 600;
}

body.dark-theme .meta-category-tag {
    background: #1e293b;
    color: #94a3b8;
}

/* أزرار الإجراء */
.notif-card-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    flex-shrink: 0;
}

.btn-open-action {
    background: #0284c7;
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-open-action:hover {
    background: #0369a1;
    transform: translateY(-1px);
}

.action-mini-group {
    display: flex;
    gap: 4px;
}

.btn-icon-subtle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: 0.2s;
}

body.dark-theme .btn-icon-subtle {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}

.btn-icon-subtle:hover {
    color: #0284c7;
    border-color: #0284c7;
}

.btn-icon-subtle.delete-btn:hover {
    color: #ef4444;
    border-color: #ef4444;
    background: #fef2f2;
}

/* حالة الفراغ */
.empty-feed-card {
    text-align: center;
    padding: 4rem 1.5rem;
    background: #ffffff;
    border-radius: 24px;
    border: 1px dashed #cbd5e1;
    color: #64748b;
}

body.dark-theme .empty-feed-card {
    background: #0f172a;
    border-color: #334155;
}

.empty-icon-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

.btn-explore-empty {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 1rem;
    background: #0284c7;
    color: white;
    text-decoration: none;
    padding: 10px 22px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
}

/* ترقيم الصفحات */
.pagination-container {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 2rem;
}

.page-link-pill {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.88rem;
    transition: 0.2s;
}

.page-link-pill.active {
    background: #0284c7;
    color: white;
    border-color: #0284c7;
}

@media (max-width: 640px) {
    .notif-feed-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .notif-card-actions {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
    }
}
</style>

<script>
function markReadDirect(id) {
    axios.post(`/student/notifications/${id}/mark-read`, {
        _token: '{{ csrf_token() }}'
    }).then(() => {
        const card = document.getElementById(`card_${id}`);
        if (card) {
            card.classList.remove('is-unread');
            const badge = card.querySelector('.badge-new-dot');
            if (badge) badge.remove();
        }
    });
}

function markAllNotificationsRead() {
    axios.post('/student/notifications/mark-all-read', {
        _token: '{{ csrf_token() }}'
    }).then(res => {
        Swal.fire({
            toast: true,
            position: 'top-start',
            icon: 'success',
            title: res.data.message || 'تم تعيين جميع الإشعارات كمقروءة',
            showConfirmButton: false,
            timer: 2000
        });
        document.querySelectorAll('.notif-feed-card.is-unread').forEach(card => {
            card.classList.remove('is-unread');
            const badge = card.querySelector('.badge-new-dot');
            if (badge) badge.remove();
        });
    });
}

function deleteNotifItem(id) {
    Swal.fire({
        title: 'حذف الإشعار؟',
        text: 'هل أنت متأكد من رغبتك في حذف هذا التنبيه من سجلك؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/student/notifications/${id}`, {
                data: { _token: '{{ csrf_token() }}' }
            }).then(res => {
                const card = document.getElementById(`card_${id}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 250);
                }
                Swal.fire({
                    toast: true,
                    position: 'top-start',
                    icon: 'success',
                    title: res.data.message || 'تم حذف الإشعار',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    });
}
</script>
@endsection
