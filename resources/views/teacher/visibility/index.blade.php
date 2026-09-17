@extends('layouts.app')

@section('title', __('Content Visibility Control') . ' | ' . config('app.name'))

@section('content')
<div class="ed-teacher-vis-container">

    <!-- الهيدر الأكاديمي -->
    <header class="ed-teacher-header">
        <div>
            <div class="ed-teacher-breadcrumbs">
                <a href="{{ route('teacher.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Teacher Portal') }}</a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size: 0.7rem;"></i>
                <span class="active">{{ __('Content Visibility') }}</span>
            </div>
            <h1>
                <i class="fa-solid fa-toggle-on" style="color: #059669;"></i> {{ __('Content Visibility & Access Control') }}
            </h1>
            <p>
                {{ __('One-touch control to instantly show or hide video lessons and documents for enrolled students.') }}
            </p>
        </div>

        <div class="ed-badge-status-chip">
            <i class="fa-solid fa-shield-halved"></i>
            <span>{{ __('Live instant update without page refresh') }}</span>
        </div>
    </header>

    <!-- جدول المحتويات وحالة الظهور -->
    <div class="ed-card-table-wrap">
        <div class="table-head-bar">
            <h3>{{ __('Lessons & Documents List') }}</h3>
            <span class="total-badge">
                {{ __('Total Items:') }} {{ $contents->total() }}
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="ed-vis-table">
                <thead>
                    <tr>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Content Title') }}</th>
                        <th>{{ __('Subject & Branch') }}</th>
                        <th>{{ __('Order') }}</th>
                        <th style="text-align: center;">{{ __('Student Visibility Status') }}</th>
                        <th style="text-align: center;">{{ __('Quick Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contents as $item)
                        @php
                            $isVideo = !empty($item->url_path) || $item->type === 'video';
                            $isVisible = (bool) $item->is_visible;
                        @endphp
                        <tr id="row_content_{{ $item->id }}">
                            <td>
                                <span class="type-pill {{ $isVideo ? 'video' : 'doc' }}">
                                    <i class="fa-solid {{ $isVideo ? 'fa-video' : 'fa-file-pdf' }}"></i>
                                    {{ $isVideo ? __('Video') : __('Booklet') }}
                                </span>
                            </td>
                            <td>
                                <div class="item-title">{{ $item->title }}</div>
                                <span class="item-channel">{{ $item->channel_name ?? __('General') }}</span>
                            </td>
                            <td>
                                <span class="subject-tag">
                                    {{ $item->subject?->name_ar ?? __('General') }}
                                </span>
                            </td>
                            <td class="font-mono">
                                #{{ $item->order }}
                            </td>
                            <td style="text-align: center;">
                                <span id="status_badge_{{ $item->id }}" class="vis-badge {{ $isVisible ? 'visible' : 'hidden' }}">
                                    <i class="fa-solid {{ $isVisible ? 'fa-circle-check' : 'fa-lock' }}"></i>
                                    <span id="status_text_{{ $item->id }}">{{ $isVisible ? __('Visible to students') : __('Hidden from students') }}</span>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" onclick="toggleVisibilityAjax({{ $item->id }}, this)" class="btn-toggle-vis {{ $isVisible ? 'btn-hide' : 'btn-show' }}">
                                    <i class="fa-solid {{ $isVisible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    <span>{{ $isVisible ? __('Hide Content') : __('Show Content') }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-table-cell">
                                <i class="fa-solid fa-inbox"></i>
                                <p>{{ __('No content available currently to adjust visibility.') }}</p>
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

<style>
.ed-teacher-vis-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-teacher-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
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

.ed-teacher-breadcrumbs .active {
    color: #059669;
    font-weight: 700;
}

.ed-teacher-header h1 {
    margin: 0 0 6px;
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
}

.ed-teacher-header p {
    margin: 0;
    color: #64748b;
    font-size: 0.88rem;
    max-width: 780px;
    line-height: 1.6;
}

.ed-badge-status-chip {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #065f46;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.ed-card-table-wrap {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.table-head-bar {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.table-head-bar h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}

.total-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
}

.ed-vis-table {
    width: 100%;
    border-collapse: collapse;
}

.ed-vis-table th {
    padding: 14px 20px;
    font-size: 0.82rem;
    font-weight: 800;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    text-align: inherit;
}

.ed-vis-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.88rem;
}

.type-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 800;
}

.type-pill.video { background: #eff6ff; color: #1e3a8a; }
.type-pill.doc { background: #fee2e2; color: #dc2626; }

.item-title {
    font-weight: 800;
    color: #0f172a;
    font-size: 0.94rem;
    margin-bottom: 2px;
}

.item-channel {
    font-size: 0.75rem;
    color: #94a3b8;
}

.subject-tag {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    color: #334155;
    font-weight: 700;
}

.vis-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 800;
}

.vis-badge.visible {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #bbf7d0;
}

.vis-badge.hidden {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-toggle-vis {
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 800;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-toggle-vis.btn-hide {
    background: #fef2f2;
    color: #dc2626;
}

.btn-toggle-vis.btn-show {
    background: #eff6ff;
    color: #1e3a8a;
}

.empty-table-cell {
    text-align: center;
    padding: 50px 20px;
    color: #94a3b8;
}

.empty-table-cell i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    display: block;
}
</style>

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

        if (isVisible) {
            badge.className = 'vis-badge visible';
            badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>{{ __("Visible to students") }}</span>';
            btn.className = 'btn-toggle-vis btn-hide';
            btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> <span>{{ __("Hide Content") }}</span>';
        } else {
            badge.className = 'vis-badge hidden';
            badge.innerHTML = '<i class="fa-solid fa-lock"></i> <span>{{ __("Hidden from students") }}</span>';
            btn.className = 'btn-toggle-vis btn-show';
            btn.innerHTML = '<i class="fa-solid fa-eye"></i> <span>{{ __("Show Content") }}</span>';
        }

        btn.disabled = false;

        Swal.fire({
            toast: true,
            position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
            icon: 'success',
            title: res.data.message || '{{ __("Visibility status updated") }}',
            showConfirmButton: false,
            timer: 2000
        });
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not update visibility.") }}' });
    }
}
</script>
@endsection
