@extends('layouts.app')

@section('title', __('سجل الطلاب الأكاديمي') . ' - ' . __('إدارة المنصة'))

@section('content')
<div class="students-registry-wrapper">
    <div class="academic-header-card">
        <div>
            <div class="badge-tag">
                <i class="fa-solid fa-users"></i>
                <span>{{ __('إدارة شؤون الطلبة') }}</span>
            </div>
            <h1 class="header-title">{{ __('سجل الطلاب الأكاديمي') }}</h1>
            <p class="header-subtitle">{{ __('استعراض السجلات الأكاديمية والبيانات العامة لطلبة المنصة.') }}</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn-classic-nav">
            <i class="fa-solid fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('إدارة الطلاب والاشتراكات') }}
        </a>
    </div>

    <div class="table-card-box">
        <div class="table-responsive">
            <table class="academic-modern-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>{{ __('اسم الطالب') }}</th>
                        <th>{{ __('البريد الإلكتروني') }}</th>
                        <th>{{ __('تاريخ الانضمام') }}</th>
                        <th style="text-align: center;">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="id-cell font-mono">#{{ $student->id }}</td>
                        <td>
                            <div class="student-info-cell">
                                <div class="student-avatar-initial">
                                    {{ mb_substr($student->name_ar ?? $student->name ?? 'ط', 0, 1) }}
                                </div>
                                <span class="student-full-name">{{ $student->name_ar ?? $student->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="email-text font-mono" dir="ltr">{{ $student->email }}</div>
                            <div style="margin-top: 4px; display: inline-flex; align-items: center; gap: 6px;">
                                @if(!empty($student->plain_password))
                                    <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 4px; padding: 1px 6px; font-size: 0.75rem; font-weight: 700; font-family: monospace;" dir="ltr">
                                        <i class="fa-solid fa-key" style="color: #d97706; font-size: 0.7rem;"></i>
                                        <span>{{ $student->plain_password }}</span>
                                    </span>
                                    <button type="button" onclick="copyProfileAllPass('{{ $student->plain_password }}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 1px 6px; font-size: 0.75rem; cursor: pointer; color: #475569;" title="{{ __('نسخ كلمة المرور') }}">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                @else
                                    <span style="background: #f1f5f9; color: #64748b; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 1px 6px; font-size: 0.72rem; font-weight: 700;" title="{{ __('مشفرة بأمان في النظام') }}">
                                        <i class="fa-solid fa-shield-halved" style="font-size: 0.7rem;"></i> {{ __('مشفرة') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="date-badge font-mono">
                                <i class="fa-regular fa-calendar-days"></i>
                                {{ $student->created_at ? $student->created_at->format('Y-m-d') : '—' }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.students.show', $student->id) }}" class="btn-view-profile">
                                <i class="fa-solid fa-id-card"></i> {{ __('الملف الشخصي') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state-cell">
                            <i class="fa-solid fa-users-slash empty-icon"></i>
                            <h4>{{ __('لا توجد سجلات طلاب حالياً') }}</h4>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="pagination-footer-box">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .students-registry-wrapper {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 10px 0 60px;
        box-sizing: border-box;
    }
    .academic-header-card {
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
    .badge-tag {
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
    .header-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .header-subtitle {
        color: #64748b;
        font-size: 0.88rem;
        margin: 0;
    }
    .btn-classic-nav {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.15s;
    }
    .btn-classic-nav:hover { background: #f8fafc; color: #0f172a; }

    .table-card-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .academic-modern-table {
        width: 100%;
        border-collapse: collapse;
        text-align: start;
        font-size: 0.88rem;
    }
    .academic-modern-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .academic-modern-table th { padding: 14px 18px; }
    .academic-modern-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .academic-modern-table tbody tr:hover { background: #f8fafc; }
    .academic-modern-table td { padding: 14px 18px; vertical-align: middle; }

    .id-cell { font-weight: 700; color: #64748b; }
    .student-info-cell { display: flex; align-items: center; gap: 12px; }
    .student-avatar-initial {
        width: 36px;
        height: 36px;
        background: #eff6ff;
        color: #1e3a8a;
        border-radius: 8px;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid #bfdbfe;
        flex-shrink: 0;
    }
    .student-full-name { font-weight: 700; color: #0f172a; }
    .email-text { color: #64748b; font-size: 0.85rem; }
    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 0.82rem;
    }
    .btn-view-profile {
        background: #eff6ff;
        color: #1e3a8a;
        border: 1px solid #bfdbfe;
        padding: 6px 14px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.82rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .btn-view-profile:hover { background: #1e3a8a; color: #ffffff; }

    .empty-state-cell {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }
    .empty-icon { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4; }

    .pagination-footer-box {
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
    }
</style>

<script>
function copyProfileAllPass(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        textArea.remove();
    }
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ __("تم نسخ كلمة المرور") }}',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        alert('{{ __("تم نسخ كلمة المرور بنجاح") }}');
    }
}
</script>
@endsection
