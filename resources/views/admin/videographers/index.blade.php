@extends('layouts.app')

@section('title', __('إدارة كادر المصورين ووحدات الإنتاج') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="admin-videographers-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 10px 4px 60px 4px;">

    {{-- 1. رأس الصفحة والمسار الأكاديمي الكلاسيكي --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.25rem; border: 1px solid #bfdbfe; flex-shrink: 0;">
                <i class="fa-solid fa-video"></i>
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; font-family: 'Alexandria', 'Cairo', sans-serif;">
                    {{ __('إدارة كادر المصورين واستوديوهات الإنتاج') }}
                </h1>
                <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ __('الرئيسية') }}</a>
                    <span>/</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ __('كادر المصورين المعتمدين') }}</span>
                    <span>•</span>
                    <span>{{ __('المخولون برفع وتوزيع المحاضرات المصورة') }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 6px; font-weight: 600; font-size: 0.84rem; text-decoration: none;">
                <i class="fa-solid fa-cloud-arrow-up" style="color: #1d4ed8;"></i>
                <span>{{ __('استوديو الرفع والتوزيع') }}</span>
            </a>
            <a href="{{ route('admin.videographers.create') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #1d4ed8; color: #ffffff; border: 1px solid #1e40af; padding: 9px 18px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; text-decoration: none; transition: background 0.15s; box-shadow: 0 1px 2px rgba(29, 78, 216, 0.15);">
                <i class="fa-solid fa-user-plus"></i>
                <span>{{ __('إضافة مصور جديد') }}</span>
            </a>
        </div>
    </div>

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 0.88rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. جدول المصورين الأكاديمي الكلاسيكي --}}
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden;">
        
        <div style="padding: 14px 20px; background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <strong style="color: #0f172a; font-size: 0.92rem;">
                <i class="fa-solid fa-users" style="color: #1d4ed8; margin-left: 6px;"></i>
                {{ __('المصورون المسجلون بالمنظومة:') }} <span style="color: #1d4ed8;">{{ $videographers->total() }}</span> {{ __('حساب') }}
            </strong>
        </div>

        @if($videographers->isEmpty())
            <div style="padding: 50px 20px; text-align: center; color: #64748b;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto 14px auto; font-size: 1.6rem; color: #94a3b8;">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">{{ __('لا يوجد مصورون مضافون حالياً') }}</h3>
                <p style="font-size: 0.84rem; color: #64748b; margin: 0 0 16px 0;">{{ __('يمكنك إضافة حساب مصور متعاقد معه لتمكينه من رفع المحاضرات وتوزيعها تلقائياً على الفروع والمواد.') }}</p>
                <a href="{{ route('admin.videographers.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 9px 20px; border-radius: 6px; font-weight: 700; font-size: 0.86rem; text-decoration: none;">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>{{ __('إضافة أول مصور الآن') }}</span>
                </a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 0.86rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 0.78rem;">
                            <th style="padding: 12px 18px; width: 44px; text-align: center;">#</th>
                            <th style="padding: 12px 16px;">{{ __('المصور / وحدة الإنتاج') }}</th>
                            <th style="padding: 12px 16px;">{{ __('البريد الإلكتروني للدخول') }}</th>
                            <th style="padding: 12px 16px;">{{ __('الجوال / واتساب') }}</th>
                            <th style="padding: 12px 16px; text-align: center;">{{ __('المحاضرات المرفوعة') }}</th>
                            <th style="padding: 12px 16px;">{{ __('تاريخ الإضافة') }}</th>
                            <th style="padding: 12px 18px; text-align: center;">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videographers as $idx => $v)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.1s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                                
                                <td style="padding: 12px 18px; text-align: center; color: #94a3b8; font-weight: 600;">
                                    {{ $videographers->firstItem() + $idx }}
                                </td>

                                <td style="padding: 12px 16px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 0.95rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-video"></i>
                                        </div>
                                        <div>
                                            <strong style="color: #0f172a; font-size: 0.88rem; display: block;">
                                                {{ $v->name }}
                                            </strong>
                                            @if($v->plain_password)
                                                <span style="font-size: 0.72rem; color: #059669; font-family: monospace;">
                                                    <i class="fa-solid fa-key" style="font-size: 0.65rem;"></i> {{ $v->plain_password }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td style="padding: 12px 16px; color: #1e293b; font-weight: 600; direction: ltr; text-align: right;">
                                    {{ $v->email }}
                                </td>

                                <td style="padding: 12px 16px; color: #475569; direction: ltr; text-align: right;">
                                    {{ $v->phone ?: '-' }}
                                </td>

                                <td style="padding: 12px 16px; text-align: center;">
                                    <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 4px; font-size: 0.76rem; font-weight: 700;">
                                        {{ $v->uploaded_contents_count }} {{ __('محاضرة') }}
                                    </span>
                                </td>

                                <td style="padding: 12px 16px; color: #64748b; font-size: 0.8rem;">
                                    {{ $v->created_at ? $v->created_at->format('Y/m/d') : '-' }}
                                </td>

                                <td style="padding: 12px 18px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        
                                        {{-- زر تعيين كلمة مرور --}}
                                        <button type="button" onclick="openResetPasswordModal('{{ $v->id }}', '{{ $v->name }}')" style="background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: grid; place-items: center;" title="{{ __('تغيير كلمة المرور') }}">
                                            <i class="fa-solid fa-key" style="font-size: 0.78rem;"></i>
                                        </button>

                                        {{-- زر حذف الحساب --}}
                                        <form action="{{ route('admin.videographers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف حساب المصور (') . $v->name . __(')؟') }}')" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #ffffff; color: #dc2626; border: 1px solid #fee2e2; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: grid; place-items: center;" title="{{ __('حذف المصور') }}">
                                                <i class="fa-regular fa-trash-can" style="font-size: 0.82rem;"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($videographers->hasPages())
                <div style="padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: center;">
                    {{ $videographers->links() }}
                </div>
            @endif
        @endif

    </div>

</div>

{{-- نافذة منبثقة لإعادة تعيين كلمة مرور المصور --}}
<div id="resetPasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 8px; max-width: 440px; width: 100%; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); overflow: hidden;">
        <div style="padding: 14px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <strong id="resetModalTitle" style="font-size: 0.95rem; color: #0f172a; font-weight: 700;">{{ __('تعيين كلمة مرور جديدة للمصور') }}</strong>
            <button type="button" onclick="closeResetPasswordModal()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="resetPasswordForm" method="POST" style="padding: 20px;">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">{{ __('كلمة المرور الجديدة') }}</label>
                <input type="text" name="new_password" required minlength="6" placeholder="Pass@123456" style="width: 100%; height: 38px; padding: 0 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; font-weight: 700; direction: ltr; text-align: right;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="closeResetPasswordModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 6px; font-weight: 600; font-size: 0.82rem; cursor: pointer;">
                    {{ __('إلغاء') }}
                </button>
                <button type="submit" style="background: #1d4ed8; color: #ffffff; border: none; padding: 7px 18px; border-radius: 6px; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                    {{ __('تحديث كلمة المرور') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResetPasswordModal(id, name) {
        document.getElementById('resetModalTitle').textContent = 'تعيين كلمة مرور للمصور: ' + name;
        document.getElementById('resetPasswordForm').action = '/admin/videographers/' + id + '/reset-password';
        document.getElementById('resetPasswordModal').style.display = 'grid';
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').style.display = 'none';
    }

    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) closeResetPasswordModal();
    });
</script>
@endsection
