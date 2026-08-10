@extends('layouts.app')

@section('title', 'سجل الإشعارات والرسائل')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-bell" style="color: var(--side-active);"></i> سجل الإشعارات والردود الأكاديمية
            </h2>
            <span style="background: #e0f2fe; color: #0284c7; padding: 5px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;">
                الإجمالي: {{ $notifications->total() ?? 0 }}
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            @forelse($notifications as $notification)
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; justify-content: space-between; align-items: flex-start; transition: 0.2s;" onmouseover="this.style.borderColor='#007bff'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div style="width: 45px; height: 45px; border-radius: 12px; background: #dbeafe; color: #1d4ed8; display: grid; place-items: center; font-size: 1.1rem; flex-shrink: 0;">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 5px;">رد جديد من إدارة المنصة / الدعم الفني</h4>
                            <p style="font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 10px;">{{ $notification->message }}</p>
                            <span style="font-size: 0.75rem; color: #94a3b8; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-regular fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('student.support') }}" style="background: #007bff; color: white; padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: inline-block; transition: 0.2s;">
                            عرض المحادثة
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="fa-solid fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                    <p style="font-size: 0.95rem; font-weight: 600;">لا توجد إشعارات أو ردود مسجلة في سِجلّك حتى الآن.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div style="margin-top: 25px; display: flex; justify-content: center;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
