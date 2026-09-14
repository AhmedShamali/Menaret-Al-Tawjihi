@extends('layouts.app')

@section('title', 'التحكم في ظهور وإخفاء المحتوى | بوابة المعلم')

@section('content')
<div class="ed-teacher-vis-container" style="max-width: 1300px; margin: 0 auto; padding: 20px 16px 60px; direction: rtl; font-family: 'Alexandria', sans-serif;">

    <!-- الهيدر -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #64748b; margin-bottom: 6px;">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">بوابة المعلم</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.7rem;"></i>
                <span style="color: #059669; font-weight: 700;">التحكم بظهور المحتوى</span>
            </div>
            <h1 style="margin: 0 0 6px; font-size: 1.8rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-toggle-on" style="color: #059669;"></i> التحكم في ظهور وإخفاء المحتوى
            </h1>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                تحكم فوري بلمسة واحدة في إظهار أو حجب الفيديوهات والملفات الدراسية عن حسابات الطلبة.
            </p>
        </div>

        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px 18px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; color: #065f46; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-shield-halved"></i>
            <span>تحديث مباشر ولحظي دون إعادة تحميل الصفحة</span>
        </div>
    </div>

    <!-- جدول المحتويات وحالة الظهور -->
    <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                قائمة الدروس والملازم الدراسية
            </h3>
            <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: #475569;">
                إجمالي العناصر: {{ $contents->total() }}
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569;">النوع</th>
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569;">عنوان المحتوى الأكاديمي</th>
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569;">المادة والفرع</th>
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569;">الترتيب</th>
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569; text-align: center;">حالة الظهور للطلبة</th>
                        <th style="padding: 14px 20px; font-size: 0.82rem; font-weight: 800; color: #475569; text-align: center;">إجراء سريع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $item)
                        @php
                            $isVideo = !empty($item->url_path) || $item->type === 'video';
                            $isVisible = (bool) $item->is_visible;
                        @endphp
                        <tr id="row_content_{{ $item->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                            <td style="padding: 16px 20px;">
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; font-size: 0.78rem; font-weight: 800; background: {{ $isVideo ? '#eff6ff' : '#fee2e2' }}; color: {{ $isVideo ? '#1d4ed8' : '#dc2626' }};">
                                    <i class="fa-solid {{ $isVideo ? 'fa-video' : 'fa-file-pdf' }}"></i>
                                    {{ $isVideo ? 'فيديو' : 'ملزمة' }}
                                </span>
                            </td>
                            <td style="padding: 16px 20px;">
                                <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; margin-bottom: 2px;">{{ $item->title }}</div>
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ $item->channel_name ?? 'عام' }}</span>
                            </td>
                            <td style="padding: 16px 20px;">
                                <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 3px 8px; border-radius: 6px; font-size: 0.78rem; color: #334155; font-weight: 700;">
                                    {{ $item->subject?->name_ar ?? 'عام' }}
                                </span>
                            </td>
                            <td style="padding: 16px 20px; font-family: monospace; font-weight: 800; color: #64748b;">
                                #{{ $item->order }}
                            </td>
                            <td style="padding: 16px 20px; text-align: center;">
                                <span id="status_badge_{{ $item->id }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; background: {{ $isVisible ? '#ecfdf5' : '#fef2f2' }}; color: {{ $isVisible ? '#059669' : '#dc2626' }}; border: 1px solid {{ $isVisible ? '#bbf7d0' : '#fecaca' }};">
                                    <i class="fa-solid {{ $isVisible ? 'fa-circle-check' : 'fa-lock' }}"></i>
                                    <span id="status_text_{{ $item->id }}">{{ $isVisible ? 'ظاهر للطلبة' : 'محجوب عن الطلبة' }}</span>
                                </span>
                            </td>
                            <td style="padding: 16px 20px; text-align: center;">
                                <button type="button" onclick="toggleVisibilityAjax({{ $item->id }}, this)" class="btn-toggle-vis" style="padding: 8px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 800; cursor: pointer; border: none; background: {{ $isVisible ? '#fef2f2' : '#eff6ff' }}; color: {{ $isVisible ? '#dc2626' : '#1d4ed8' }}; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;">
                                    <i class="fa-solid {{ $isVisible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    <span>{{ $isVisible ? 'حجب الدرس' : 'إتاحة الدرس' }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                                <i class="fa-solid fa-inbox" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></i>
                                <p style="margin: 0; font-weight: 700;">لا يوجد محتوى مدرج حالياً لتعديل ظهوره.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contents->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #f1f5f9;">
                {{ $contents->links() }}
            </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
async function toggleVisibilityAjax(id, btn) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const res = await axios.post(`{{ url('teacher/visibility/toggle') }}/${id}`, {
            _token: '{{ csrf_token() }}'
        });

        const isVisible = res.data.is_visible;
        const badge = document.getElementById(`status_badge_${id}`);
        const text = document.getElementById(`status_text_${id}`);

        if (isVisible) {
            badge.style.background = '#ecfdf5';
            badge.style.color = '#059669';
            badge.style.borderColor = '#bbf7d0';
            badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>ظاهر للطلبة</span>';
            btn.style.background = '#fef2f2';
            btn.style.color = '#dc2626';
            btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> <span>حجب الدرس</span>';
        } else {
            badge.style.background = '#fef2f2';
            badge.style.color = '#dc2626';
            badge.style.borderColor = '#fecaca';
            badge.innerHTML = '<i class="fa-solid fa-lock"></i> <span>محجوب عن الطلبة</span>';
            btn.style.background = '#eff6ff';
            btn.style.color = '#1d4ed8';
            btn.innerHTML = '<i class="fa-solid fa-eye"></i> <span>إتاحة الدرس</span>';
        }

        btn.disabled = false;

        Swal.fire({
            toast: true,
            position: 'top-start',
            icon: 'success',
            title: res.data.message,
            showConfirmButton: false,
            timer: 2000
        });
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر تعديل حالة الظهور.' });
    }
}
</script>
@endsection
