@extends('layouts.app')

@section('title', __('Notifications & Alerts Center') . ' | ' . config('app.name'))

@section('content')
<div class="notifications-page-container">

    <!-- كرت الترويسة الرئيسية - Classic Academic Hero -->
    <div class="notif-hero-card">
        <div class="hero-content">
            <div class="badge-tag">
                <i class="fa-solid fa-bell-ring"></i>
                <span>{{ __('Instant Notifications Center') }}</span>
            </div>
            <h1 class="hero-title">{{ __('Academic & Financial Alerts') }}</h1>
            <p class="hero-subtitle">
                {{ __('Stay updated with teacher responses, course activations, and ministerial exam schedules.') }}
            </p>
        </div>

        <div class="hero-actions">
            @if($counts['unread'] > 0)
                <button type="button" class="btn-mark-all" onclick="markAllNotificationsRead()">
                    <i class="fa-solid fa-check-double"></i>
                    <span>{{ __('Mark all as read') }} ({{ $counts['unread'] }})</span>
                </button>
            @else
                <div class="all-read-badge">
                    <i class="fa-solid fa-circle-check text-success"></i>
                    <span>{{ __('All notifications are read') }}</span>
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
                <span>{{ __('All Notifications') }}</span>
                <span class="pill-count">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'unread']) }}" 
               class="filter-pill {{ $filter === 'unread' ? 'active' : '' }}">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>{{ __('Unread') }}</span>
                @if($counts['unread'] > 0)
                    <span class="pill-count unread-badge">{{ $counts['unread'] }}</span>
                @else
                    <span class="pill-count">0</span>
                @endif
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'message']) }}" 
               class="filter-pill {{ $filter === 'message' ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i>
                <span>{{ __('Messages & Teachers') }}</span>
                <span class="pill-count">{{ $counts['message'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'payment']) }}" 
               class="filter-pill {{ $filter === 'payment' ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i>
                <span>{{ __('Subscriptions & Payments') }}</span>
                <span class="pill-count">{{ $counts['payment'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'exam']) }}" 
               class="filter-pill {{ $filter === 'exam' ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>{{ __('Exams & Results') }}</span>
                <span class="pill-count">{{ $counts['exam'] }}</span>
            </a>

            <a href="{{ route('student.notifications.index', ['filter' => 'academic']) }}" 
               class="filter-pill {{ $filter === 'academic' ? 'active' : '' }}">
                <i class="fa-solid fa-fire"></i>
                <span>{{ __('Achievements & Progress') }}</span>
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

                $categoryLabel = match($type) {
                    'payment' => __('Financial Transaction'),
                    'message' => __('Private Message'),
                    'exam' => __('Ministerial Exam'),
                    'streak' => __('Study Streak'),
                    'academic' => __('Academic Achievement'),
                    default => __('System Alert')
                };
            @endphp

            <div class="notif-feed-card {{ $isUnread ? 'is-unread' : '' }}" 
                 id="card_{{ $notif->id }}" 
                 onclick="window.location.href='{{ route('notifications.open', $notif->id) }}'">
                <div class="notif-card-start">
                    <div class="notif-icon-avatar {{ $iconTypeClass }}">
                        <i class="{{ $iconClass }}"></i>
                    </div>

                    <div class="notif-body-content">
                        <div class="notif-headline-row">
                            <h3 class="notif-headline">{{ $notif->title }}</h3>
                            @if($isUnread)
                                <span class="badge-new-dot">{{ __('New') }}</span>
                            @endif
                        </div>

                        <p class="notif-text-message">
                            {{ $notif->message }}
                        </p>

                        <div class="notif-meta-tags">
                            <span class="meta-time">
                                <i class="fa-regular fa-clock"></i>
                                {{ $notif->created_at ? \Carbon\Carbon::parse($notif->created_at)->diffForHumans() : __('Just now') }}
                            </span>
                            <span class="meta-category-tag">
                                {{ $categoryLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء السريع -->
                <div class="notif-card-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('notifications.open', $notif->id) }}" 
                       class="btn-open-action" 
                       title="{{ __('Open') }}">
                        <span>{{ __('Open') }}</span>
                        <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                    </a>

                    <div class="action-mini-group">
                        @if($isUnread)
                            <button type="button" 
                                    class="btn-icon-subtle" 
                                    onclick="event.stopPropagation(); markReadDirect('{{ $notif->id }}')" 
                                    title="{{ __('Mark as read') }}">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        @endif

                        <button type="button" 
                                class="btn-icon-subtle delete-btn" 
                                onclick="event.stopPropagation(); deleteNotifItem('{{ $notif->id }}')" 
                                title="{{ __('Delete') }}">
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
                <h3>{{ __('No notifications in this category') }}</h3>
                <p>{{ __('All new alerts, replies, and academic updates will appear right here.') }}</p>
                <a href="{{ route('student.courses.catalog') }}" class="btn-explore-empty">
                    <i class="fa-solid fa-compass"></i> {{ __('Explore Courses & Exams') }}
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
.notifications-page-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto 50px;
    padding: 0;
    box-sizing: border-box;
}

/* 1. كرت الترويسة الأكاديمية */
.notif-hero-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 24px 30px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.hero-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.hero-subtitle {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

.btn-mark-all {
    background: #1e3a8a;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    transition: all 0.2s ease;
}

.btn-mark-all:hover {
    background: #172554;
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

/* 2. شريط التصنيفات */
.filter-tabs-wrapper {
    margin-bottom: 24px;
}

.filter-tabs-scroll {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 6px;
    scrollbar-width: thin;
}

.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 10px 18px;
    border-radius: 14px;
    color: #475569;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 700;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.filter-pill:hover {
    border-color: #cbd5e1;
    color: #1e3a8a;
}

.filter-pill.active {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
}

.pill-count {
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.74rem;
    font-weight: 800;
}

.filter-pill.active .pill-count {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.pill-count.unread-badge {
    background: #ef4444;
    color: #ffffff;
}

/* 3. قائمة الإشعارات */
.notifications-feed-lane {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.notif-feed-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notif-feed-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
    transform: translateY(-1px);
}

.notif-feed-card.is-unread {
    border-inline-start: 4px solid #1e3a8a;
    background: #f8fafc;
}

.notif-card-start {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
    min-width: 0;
}

.notif-icon-avatar {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.notif-icon-avatar.type-payment { background: #ecfdf5; color: #059669; }
.notif-icon-avatar.type-message { background: #eff6ff; color: #1e3a8a; }
.notif-icon-avatar.type-exam { background: #fffbeb; color: #d97706; }
.notif-icon-avatar.type-streak { background: #fff1f2; color: #e11d48; }
.notif-icon-avatar.type-academic { background: #faf5ff; color: #9333ea; }
.notif-icon-avatar.type-system { background: #f1f5f9; color: #475569; }

.notif-body-content {
    flex: 1;
    min-width: 0;
}

.notif-headline-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.notif-headline {
    font-size: 0.96rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.badge-new-dot {
    background: #ef4444;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 1px 7px;
    border-radius: 10px;
}

.notif-text-message {
    font-size: 0.86rem;
    color: #475569;
    margin: 0 0 8px;
    line-height: 1.5;
}

.notif-meta-tags {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.74rem;
    color: #94a3b8;
}

.meta-time {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.meta-category-tag {
    background: #f1f5f9;
    color: #64748b;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
}

/* أزرار الإجراء */
.notif-card-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.btn-open-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #1e3a8a;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    transition: all 0.2s;
}

.btn-open-action:hover {
    background: #1e3a8a;
    color: #ffffff;
    border-color: #1e3a8a;
}

.action-mini-group {
    display: flex;
    gap: 6px;
}

.btn-icon-subtle {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.btn-icon-subtle:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.btn-icon-subtle.delete-btn:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}

/* الحالة الفارغة */
.empty-feed-card {
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 20px;
    padding: 60px 20px;
}

.empty-icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: grid;
    place-items: center;
    font-size: 1.8rem;
    margin: 0 auto 16px;
}

.empty-feed-card h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.empty-feed-card p {
    font-size: 0.86rem;
    color: #64748b;
    margin: 0 0 20px;
}

.btn-explore-empty {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1e3a8a;
    color: #ffffff;
    padding: 10px 22px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 0.86rem;
    font-weight: 700;
}

/* الترقيم */
.pagination-container {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 28px;
}

.page-link-pill {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    display: grid;
    place-items: center;
    font-weight: 700;
    font-size: 0.86rem;
    text-decoration: none;
    transition: all 0.2s;
}

.page-link-pill.active {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #ffffff;
}

@media (max-width: 768px) {
    .notif-feed-card {
        flex-direction: column;
        align-items: stretch;
    }
    .notif-card-actions {
        justify-content: flex-end;
        border-top: 1px solid #f1f5f9;
        padding-top: 10px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function markReadDirect(id) {
    axios.post(`/student/notifications/${id}/read`, {
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
            position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
            icon: 'success',
            title: res.data.message || '{{ __("All notifications marked as read") }}',
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
        title: '{{ __("Delete Notification?") }}',
        text: '{{ __("Are you sure you want to delete this notification?") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("Yes, delete") }}',
        cancelButtonText: '{{ __("Cancel") }}'
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
                    position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
                    icon: 'success',
                    title: res.data.message || '{{ __("Notification deleted") }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }
    });
}
</script>
@endsection
