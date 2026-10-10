@extends('layouts.app')

@section('title', __('لوحة تحكم استوديو التصوير') . ' | ' . __(config('app.name', 'Step by Step')))

@section('content')
<div class="videographer-dashboard-wrapper" style="max-width: 1300px; margin: 0 auto; padding: 10px 0 40px 0;">

    {{-- 1. ترويسة لوحة التحكم - كلاسيكي رايق --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 24px 28px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 18px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.4rem; border: 1px solid #bfdbfe;">
                <i class="fa-solid fa-video"></i>
            </div>
            <div>
                <h1 style="font-size: 1.35rem; font-weight: 700; color: var(--ed-text-main); margin: 0 0 4px 0; font-family: 'Alexandria', sans-serif;">
                    {{ __('استوديو الإنتاج وتصوير المحاضرات') }}
                </h1>
                <p style="font-size: 0.86rem; color: var(--ed-text-muted); margin: 0;">
                    {{ __('مرحباً بك،') }} <strong style="color: var(--ed-text-main);">{{ auth()->user()->name }}</strong> • {{ __('رفع وتوزيع المحاضرات المصورة والدوسيات على الفروع والمواد بضغطة زر واحدة') }}
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 18px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; text-decoration: none; transition: background 0.15s; box-shadow: 0 2px 4px rgba(29, 78, 216, 0.15);">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>{{ __('رفع وتوزيع محاضرة جديدة') }}</span>
            </a>
            <a href="{{ route('videographer.contents.index') }}" style="display: inline-flex; align-items: center; gap: 8px; background: var(--ed-surface); color: var(--ed-text-main); border: 1px solid var(--ed-border); padding: 10px 16px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; text-decoration: none; transition: background 0.15s;">
                <i class="fa-solid fa-film"></i>
                <span>{{ __('سجل المحاضرات') }}</span>
            </a>
        </div>
    </div>

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 12px; padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 0.9rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. بطاقات المؤشرات الأكاديمية (KPIs) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 26px;">
        
        <!-- إجمالي الفيديوهات -->
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 14px; padding: 20px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ed-text-muted); margin-bottom: 6px;">
                    {{ __('المحاضرات المصورة المرفوعة') }}
                </span>
                <span style="font-size: 1.7rem; font-weight: 800; color: #1d4ed8; font-family: 'Alexandria', sans-serif;">
                    {{ number_format($stats['total_videos'] ?? 0) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-video"></i>
            </div>
        </div>

        <!-- الملازم والدوسيات المرفقة -->
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 14px; padding: 20px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ed-text-muted); margin-bottom: 6px;">
                    {{ __('الدوسيات والملفات المرفقة') }}
                </span>
                <span style="font-size: 1.7rem; font-weight: 800; color: #059669; font-family: 'Alexandria', sans-serif;">
                    {{ number_format($stats['total_files'] ?? 0) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
        </div>

        <!-- المواد المغطاة -->
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 14px; padding: 20px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ed-text-muted); margin-bottom: 6px;">
                    {{ __('المواد الدراسية المغطاة') }}
                </span>
                <span style="font-size: 1.7rem; font-weight: 800; color: #d97706; font-family: 'Alexandria', sans-serif;">
                    {{ number_format($stats['subjects_covered'] ?? 0) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #fffbeb; color: #d97706; display: grid; place-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- إجمالي المشاهدات -->
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: 14px; padding: 20px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="display: block; font-size: 0.82rem; font-weight: 600; color: var(--ed-text-muted); margin-bottom: 6px;">
                    {{ __('إجمالي مشاهدات الطلاب') }}
                </span>
                <span style="font-size: 1.7rem; font-weight: 800; color: #7c3aed; font-family: 'Alexandria', sans-serif;">
                    {{ number_format($stats['total_views'] ?? 0) }}
                </span>
            </div>
            <div style="width: 46px; height: 46px; border-radius: 12px; background: #f5f3ff; color: #7c3aed; display: grid; place-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>

    </div>

    {{-- 3. لافتة إرشادية مميزة لطريقة التوزيع الآلي --}}
    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 14px; padding: 18px 24px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #e2e8f0; color: #334155; display: grid; place-items: center; font-size: 1.15rem; flex-shrink: 0;">
                <i class="fa-solid fa-network-wired"></i>
            </div>
            <div>
                <strong style="display: block; font-size: 0.92rem; color: #0f172a; margin-bottom: 2px;">
                    {{ __('ميزة التوزيع التلقائي المتعدد على الفروع الأكاديمية') }}
                </strong>
                <span style="font-size: 0.82rem; color: #64748b;">
                    {{ __('عند تصوير محاضرة مشتركة (مثل اللغة الإنجليزية أو العربية)، حدد مربعات الفروع كالعلمي والأدبي والصناعي، وستظهر المحاضرة فوراً لكافة المدرسين والطلاب في تلك الفروع معاً دون تكرار الرفع.') }}
                </span>
            </div>
        </div>
        <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #0f172a; color: #ffffff; padding: 8px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-decoration: none; white-space: nowrap;">
            <i class="fa-solid fa-arrow-left"></i>
            <span>{{ __('ابدأ الرفع والتوزيع الآن') }}</span>
        </a>
    </div>

    {{-- 4. جدول آخر المحاضرات المرفوعة --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); box-shadow: var(--ed-shadow-sm); overflow: hidden;">
        
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--ed-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fafafa;">
            <div>
                <h2 style="font-size: 1.05rem; font-weight: 700; color: var(--ed-text-main); margin: 0; font-family: 'Alexandria', sans-serif;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #1d4ed8; margin-left: 6px;"></i>
                    {{ __('آخر المحاضرات التي قمت برفعها') }}
                </h2>
                <span style="font-size: 0.78rem; color: var(--ed-text-muted);">
                    {{ __('سجل أحدث المحتويات التعليمية المرتبطة بحسابك') }}
                </span>
            </div>

            <a href="{{ route('videographer.contents.index') }}" style="font-size: 0.82rem; font-weight: 600; color: #1d4ed8; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <span>{{ __('عرض كافة المحاضرات') }}</span>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
            </a>
        </div>

        @if($recentContents->isEmpty())
            <div style="padding: 48px 20px; text-align: center; color: var(--ed-text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto 16px auto; font-size: 1.8rem; color: #94a3b8;">
                    <i class="fa-solid fa-video-slash"></i>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--ed-text-main); margin: 0 0 6px 0;">{{ __('لا توجد محاضرات مرفوعة حتى الآن') }}</h3>
                <p style="font-size: 0.85rem; color: var(--ed-text-muted); margin: 0 0 18px 0;">{{ __('ابدأ برفع أول محاضرة مصورة وتوزيعها على الفروع والمواد الأكاديمية') }}</p>
                <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; text-decoration: none;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('رفع أول محاضرة الآن') }}</span>
                </a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--ed-border); color: #475569; font-weight: 600; font-size: 0.8rem;">
                            <th style="padding: 14px 20px;">{{ __('المحاضرة / الدرس') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الفرع الأكاديمي') }}</th>
                            <th style="padding: 14px 16px;">{{ __('المادة') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الملفات المرفقة') }}</th>
                            <th style="padding: 14px 16px; text-align: center;">{{ __('حالة العرض للطلاب') }}</th>
                            <th style="padding: 14px 16px;">{{ __('المشاهدات') }}</th>
                            <th style="padding: 14px 16px;">{{ __('تاريخ النشر') }}</th>
                            <th style="padding: 14px 20px; text-align: center;">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--ed-border);">
                        @foreach($recentContents as $content)
                            @php
                                $isVis = (bool) ($content->is_visible ?? true);
                            @endphp
                            <tr id="content_row_{{ $content->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 20px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-play"></i>
                                        </div>
                                        <div>
                                            <strong style="color: var(--ed-text-main); font-weight: 600; display: block; font-size: 0.9rem;">
                                                {{ $content->title }}
                                            </strong>
                                            <div style="display: flex; gap: 8px; align-items: center; font-size: 0.74rem; color: #64748b; margin-top: 3px; flex-wrap: wrap;">
                                                @if($content->channel_name)
                                                    <span><i class="fa-solid fa-camera"></i> {{ $content->channel_name }}</span>
                                                @endif
                                                @if(!empty($content->branches_count) && $content->branches_count > 1)
                                                    <span style="background: #eff6ff; color: #1d4ed8; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                                                        <i class="fa-solid fa-layer-group"></i> {{ $content->branches_count }} {{ __('فروع') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="display: inline-block; background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
                                        {{ $content->subject?->stage?->name ?? __('متعدد الفروع') }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="color: #0f172a; font-weight: 600; font-size: 0.85rem;">
                                        {{ $content->subject?->name_ar ?? $content->subject?->name ?? '-' }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        @if($content->url_path)
                                            <span title="{{ __('فيديو مرفوع أو رابط') }}" style="display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 600;">
                                                <i class="fa-solid fa-video"></i> {{ __('فيديو') }}
                                            </span>
                                        @endif
                                        @if($content->pdf_path)
                                            <a href="{{ asset('storage/' . $content->pdf_path) }}" target="_blank" title="{{ __('ملف الدوسية PDF') }}" style="display: inline-flex; align-items: center; gap: 4px; background: #ecfdf5; color: #059669; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 600; text-decoration: none;">
                                                <i class="fa-solid fa-file-pdf"></i> {{ __('دوسية') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    <button type="button" 
                                            id="dash_vis_btn_{{ $content->id }}" 
                                            onclick="toggleVideographerVisibility({{ $content->id }}, this)" 
                                            class="vis-toggle-pill {{ $isVis ? 'is-visible' : 'is-hidden' }}" 
                                            title="{{ $isVis ? __('انقر لقفل وحجب المحاضرة فوراً عن الطلاب') : __('انقر لإتاحة وعرض المحاضرة فوراً للطلاب') }}">
                                        <i class="fa-solid {{ $isVis ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        <span>{{ $isVis ? __('متاح للطلبة') : __('محجوب عن الطلبة') }}</span>
                                    </button>
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-weight: 600; font-size: 0.84rem;">
                                    <i class="fa-regular fa-eye" style="margin-left: 4px;"></i> {{ number_format($content->views_count) }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 0.8rem;">
                                    {{ $content->created_at ? $content->created_at->format('Y-m-d') : '-' }}
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    <form action="{{ route('videographer.contents.destroy', $content->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذه المحاضرة نهائياً من قاعدة البيانات والسيرفر؟') }}')" style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: 1px solid #fee2e2; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: background 0.15s;" title="{{ __('حذف نهائي') }}">
                                            <i class="fa-regular fa-trash-can" style="font-size: 0.85rem;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

</div>

<script>
async function toggleVideographerVisibility(id, btn) {
    const pillBtn = btn || document.getElementById('dash_vis_btn_' + id);
    const origHtml = pillBtn ? pillBtn.innerHTML : '';
    if (pillBtn) {
        pillBtn.disabled = true;
        pillBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>{{ __("جاري التحديث...") }}</span>';
    }

    try {
        const response = await fetch('/videographer/contents/' + id + '/toggle-visibility', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            const isVis = Boolean(data.is_visible);
            if (pillBtn) {
                pillBtn.className = 'vis-toggle-pill ' + (isVis ? 'is-visible' : 'is-hidden');
                pillBtn.title = isVis ? '{{ __("انقر لقفل وحجب المحاضرة فوراً عن الطلاب") }}' : '{{ __("انقر لإتاحة وعرض المحاضرة فوراً للطلاب") }}';
                pillBtn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-eye-slash'}"></i><span>${isVis ? '{{ __("متاح للطلبة") }}' : '{{ __("محجوب عن الطلبة") }}'}</span>`;
                pillBtn.disabled = false;
            }

            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
                    icon: 'success',
                    title: data.message || '{{ __("تم تحديث حالة العرض للطلاب بنجاح") }}',
                    showConfirmButton: false,
                    timer: 2200
                });
            }
        } else {
            throw new Error(data.error || 'حدث خطأ أثناء التحديث');
        }
    } catch (err) {
        if (pillBtn) {
            pillBtn.disabled = false;
            pillBtn.innerHTML = origHtml;
        }
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: err.message || 'تعذر تحديث حالة العرض حالياً.'
            });
        }
    }
}
</script>

<style>
.vis-toggle-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.vis-toggle-pill.is-visible {
    background: #ecfdf5;
    color: #065f46;
    border: 1.5px solid #a7f3d0;
}
.vis-toggle-pill.is-visible:hover {
    background: #d1fae5;
    border-color: #6ee7b7;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(16, 185, 129, 0.15);
}

.vis-toggle-pill.is-hidden {
    background: #fef2f2;
    color: #991b1b;
    border: 1.5px solid #fecaca;
}
.vis-toggle-pill.is-hidden:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(239, 68, 68, 0.15);
}
</style>
@endsection
