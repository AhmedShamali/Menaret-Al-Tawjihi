@extends('layouts.app')

@section('title', 'سجل الإشعارات والتنبيهات الأكاديمية')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    * { font-family: 'Alexandria', sans-serif; }

    .notifications-wrapper {
        max-width: 960px;
        margin: 0 auto;
        padding: 10px 0 40px;
        direction: rtl;
    }

    .notif-header-card {
        background: white;
        border-radius: 22px;
        padding: 26px 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .notif-item-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .notif-item-card:hover {
        border-color: #0284c7;
        box-shadow: 0 8px 25px rgba(2, 132, 199, 0.06);
        transform: translateY(-2px);
    }

    .notif-item-card.unread {
        background: #f0f9ff;
        border-color: #bae6fd;
        border-right: 4px solid #0284c7;
    }

    .notif-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .icon-msg { background: #e0f2fe; color: #0284c7; }
    .icon-exam { background: #fef3c7; color: #d97706; }
    .icon-success { background: #dcfce7; color: #16a34a; }
</style>

<div class="notifications-wrapper">
    <!-- Header -->
    <div class="notif-header-card">
        <div>
            <span style="background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700;">
                <i class="fa-solid fa-bell"></i> مركز التنبيهات
            </span>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 8px 0 4px;">
                الإشعارات والتنبيهات الأكاديمية
            </h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
                تابع ردود معلميك، تحديثات الحصص، ونتائج امتحانات الثانوية العامة فور صدورها.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="markAllRead()" style="background: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-check-double"></i> تحديد الكل كمقروء
            </button>
        </div>
    </div>

    <!-- قائمة الإشعارات -->
    <div>
        @forelse($notifications as $item)
            @php
                $isMsg = ($item instanceof \App\Models\Message);
                $isUnread = $isMsg ? !$item->is_read : is_null($item->read_at);
                $content = $isMsg 
                    ? $item->message 
                    : ($item->data['message'] ?? $item->data['title'] ?? 'إشعار أكاديمي جديد');
                $senderType = $isMsg ? $item->sender_type : ($item->data['type'] ?? 'system');
                $title = $senderType === 'teacher' ? 'رسالة جديدة من معلم المادة' : ($senderType === 'admin' ? 'رد من إدارة المنصة والدعم' : 'تنبيه أكاديمي');
                $url = $isMsg 
                    ? ($senderType === 'teacher' ? route('student.chat.teacher', $item->teacher_id ?? 1) : route('student.support'))
                    : ($item->data['url'] ?? route('student.dashboard'));
            @endphp

            <div class="notif-item-card {{ $isUnread ? 'unread' : '' }}" id="notif_card_{{ $item->id }}">
                <div style="display: flex; gap: 16px; align-items: flex-start; flex: 1;">
                    <div class="notif-icon-circle {{ $senderType === 'teacher' ? 'icon-msg' : ($senderType === 'admin' ? 'icon-success' : 'icon-exam') }}">
                        @if($senderType === 'teacher')
                            <i class="fa-solid fa-chalkboard-user"></i>
                        @elseif($senderType === 'admin')
                            <i class="fa-solid fa-headset"></i>
                        @else
                            <i class="fa-solid fa-bell"></i>
                        @endif
                    </div>

                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                            <h4 style="margin: 0; font-size: 0.98rem; font-weight: 800; color: #1e293b;">{{ $title }}</h4>
                            @if($isUnread)
                                <span style="background: #ef4444; color: white; padding: 1px 8px; border-radius: 10px; font-size: 0.65rem; font-weight: 700;">جديد</span>
                            @endif
                        </div>
                        <p style="margin: 0 0 10px; font-size: 0.88rem; color: #475569; line-height: 1.6;">
                            {{ $content }}
                        </p>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-regular fa-clock"></i> {{ $item->created_at ? $item->created_at->diffForHumans() : 'الآن' }}
                        </span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                    <a href="{{ $url }}" onclick="markSingleRead('{{ $item->id }}')" style="background: #0284c7; color: white; padding: 8px 18px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        فتح المحادثة <i class="fa-solid fa-arrow-left" style="font-size: 0.75rem;"></i>
                    </a>
                    @if($isUnread)
                        <button onclick="markSingleRead('{{ $item->id }}')" style="background: none; border: none; color: #64748b; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">
                            تعيين كمقروء
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 70px 20px; background: white; border-radius: 22px; border: 1px dashed #cbd5e1; color: #94a3b8;">
                <i class="fa-solid fa-bell-slash" style="font-size: 3.5rem; margin-bottom: 14px; display: block; opacity: 0.35;"></i>
                <h4 style="margin: 0 0 6px; font-weight: 800; color: #64748b; font-size: 1.1rem;">صندوق التنبيهات فارغ</h4>
                <p style="margin: 0; font-size: 0.88rem;">لا توجد إشعارات أو ردود مسجلة في حسابك حتى الآن.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
        <div style="margin-top: 30px; display: flex; justify-content: center;">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script>
function markSingleRead(id) {
    axios.post(`/student/notifications/${id}/mark-read`, {
        _token: '{{ csrf_token() }}'
    }).then(() => {
        const card = document.getElementById(`notif_card_${id}`);
        if (card) {
            card.classList.remove('unread');
        }
    });
}

function markAllRead() {
    axios.post('/student/notifications/mark-all-read', {
        _token: '{{ csrf_token() }}'
    }).then(res => {
        Swal.fire({
            toast: true,
            position: 'top-start',
            icon: 'success',
            title: res.data.message || 'تم تعيين جميع التنبيهات كمقروءة',
            showConfirmButton: false,
            timer: 2000
        });
        document.querySelectorAll('.notif-item-card.unread').forEach(el => el.classList.remove('unread'));
    });
}
</script>
@endsection
