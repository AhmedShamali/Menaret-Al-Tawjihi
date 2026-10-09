@extends('layouts.app')

@section('title', __('مكتبة وسجل المحاضرات المرفوعة') . ' | ' . __(config('app.name', 'Step by Step')))

@section('content')
<div class="videographer-library-wrapper" style="max-width: 1300px; margin: 0 auto; padding: 10px 0 50px 0;">

    {{-- 1. رأس الصفحة والمسار --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 0.82rem; color: var(--ed-text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('videographer.dashboard') }}" style="color: #1d4ed8; text-decoration: none;">{{ __('لوحة المصور') }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.7rem;"></i>
                <span>{{ __('مكتبة المحاضرات') }}</span>
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 700; color: var(--ed-text-main); margin: 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-film" style="color: #1d4ed8;"></i>
                {{ __('مكتبة وسجل المحاضرات المصورة المرفوعة') }}
            </h1>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="purgeAllVideographerContents()" style="display: inline-flex; align-items: center; gap: 8px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 3px rgba(220, 38, 38, 0.08);" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" title="{{ __('حذف وتصفير كافة المحاضرات الخاصة بك نهائياً من قاعدة البيانات والسيرفر') }}">
                <i class="fa-solid fa-trash-can"></i>
                <span>{{ __('حذف وتصفير كافة المحتويات') }}</span>
            </button>

            <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; text-decoration: none; transition: background 0.15s; box-shadow: 0 2px 4px rgba(29, 78, 216, 0.15);">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>{{ __('رفع وتوزيع محاضرة جديدة') }}</span>
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

    {{-- 2. شريط البحث والتصفية --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 18px 24px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm);">
        <form action="{{ route('videographer.contents.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto; gap: 14px; align-items: end;">
            
            {{-- تصفية بالفرع الأكاديمي --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    {{ __('الفرع الأكاديمي') }}
                </label>
                <select name="stage_id" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الفروع') }}</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
                            {{ $stage->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- البحث بالنص --}}
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    {{ __('بحث باسم المحاضرة أو المادة') }}
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 13px; color: #94a3b8; font-size: 0.9rem;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث عن عنوان المحاضرة أو اسم المادة...') }}" class="uni-input" style="width: 100%; height: 42px; padding: 0 36px 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a;">
                </div>
            </div>

            {{-- تصفية بحالة الظهور للطلاب --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-eye" style="color: #64748b; margin-left: 4px;"></i> {{ __('حالة العرض للطلاب') }}
                </label>
                <select name="visibility" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الحالات') }}</option>
                    <option value="visible" {{ request('visibility') == 'visible' ? 'selected' : '' }}>🟢 {{ __('متاح ومعروض للطلاب') }}</option>
                    <option value="hidden" {{ request('visibility') == 'hidden' ? 'selected' : '' }}>🔒 {{ __('محجوب ومقفل عن الطلاب') }}</option>
                </select>
            </div>

            {{-- أزرار التصفية وإعادة الضبط --}}
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #1d4ed8; color: #ffffff; border: none; height: 42px; padding: 0 18px; border-radius: 8px; font-weight: 600; font-size: 0.86rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-filter"></i>
                    <span>{{ __('تصفية') }}</span>
                </button>
                @if(request()->hasAny(['stage_id', 'subject_id', 'search', 'visibility']))
                    <a href="{{ route('videographer.contents.index') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; height: 42px; padding: 0 14px; border-radius: 8px; font-weight: 600; font-size: 0.86rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="{{ __('إلغاء الفلتر') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- 3. قائمة المحاضرات --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); box-shadow: var(--ed-shadow-sm); overflow: hidden;">
        
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--ed-border); background: #fafafa; display: flex; align-items: center; justify-content: space-between;">
            <strong style="color: var(--ed-text-main); font-size: 0.95rem;">
                <i class="fa-solid fa-list" style="color: #1d4ed8; margin-left: 6px;"></i>
                {{ __('إجمالي المحتويات:') }} <span style="color: #1d4ed8;">{{ $contents->total() }}</span> {{ __('محاضرة') }}
            </strong>
        </div>

        @if($contents->isEmpty())
            <div style="padding: 56px 20px; text-align: center; color: var(--ed-text-muted);">
                <div style="width: 68px; height: 68px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto 16px auto; font-size: 2rem; color: #94a3b8;">
                    <i class="fa-solid fa-film"></i>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--ed-text-main); margin: 0 0 6px 0;">{{ __('لم يتم العثور على أي محاضرات مطابقة') }}</h3>
                <p style="font-size: 0.86rem; color: var(--ed-text-muted); margin: 0 0 18px 0;">{{ __('جرّب تغيير معايير البحث أو ابدأ برفع محاضرة جديدة الآن') }}</p>
                <a href="{{ route('videographer.contents.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 22px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; text-decoration: none;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('رفع محاضرة جديدة') }}</span>
                </a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--ed-border); color: #475569; font-weight: 600; font-size: 0.8rem;">
                            <th style="padding: 14px 20px;">#</th>
                            <th style="padding: 14px 16px;">{{ __('المحاضرة') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الفرع الأكاديمي') }}</th>
                            <th style="padding: 14px 16px;">{{ __('المادة') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الملف والمرفقات') }}</th>
                            <th style="padding: 14px 16px; text-align: center;">{{ __('إتاحة وعرض الفيديو للطلاب') }}</th>
                            <th style="padding: 14px 16px;">{{ __('المشاهدات') }}</th>
                            <th style="padding: 14px 16px;">{{ __('تاريخ الرفع') }}</th>
                            <th style="padding: 14px 20px; text-align: center;">{{ __('معاينة / إجراء') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contents as $index => $item)
                            @php
                                $isVis = (bool) ($item->is_visible ?? true);
                            @endphp
                            <tr id="content_row_{{ $item->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 20px; color: #94a3b8; font-weight: 600; font-size: 0.8rem;">
                                    {{ $contents->firstItem() + $index }}
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 0.95rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-play"></i>
                                        </div>
                                        <div>
                                            <strong style="color: var(--ed-text-main); font-weight: 600; display: block; font-size: 0.88rem;">
                                                {{ $item->title }}
                                            </strong>
                                            <div style="display: flex; gap: 8px; align-items: center; font-size: 0.74rem; color: #64748b; margin-top: 2px;">
                                                @if($item->channel_name)
                                                    <span><i class="fa-solid fa-camera"></i> {{ $item->channel_name }}</span>
                                                @endif
                                                @if($item->file_size)
                                                    <span>• {{ $item->file_size }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <span style="display: inline-block; background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px; font-size: 0.78rem; font-weight: 600;">
                                        {{ $item->subject?->stage?->name ?? __('غير محدد') }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <strong style="color: #0f172a; font-size: 0.86rem;">
                                        {{ $item->subject?->name_ar ?? $item->subject?->name ?? '-' }}
                                    </strong>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        @if($item->url_path)
                                            @php
                                                $streamUrl = \App\Support\MediaHelper::videoStreamUrl($item->url_path);
                                            @endphp
                                            <button type="button" 
                                                    data-title="{{ $item->title }}" 
                                                    data-url="{{ $streamUrl }}" 
                                                    onclick="openVideoPreview(this.getAttribute('data-title'), this.getAttribute('data-url'))" 
                                                    style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-eye"></i> {{ __('معاينة') }}
                                            </button>
                                        @endif
                                        @if($item->pdf_path)
                                            <a href="{{ \App\Support\MediaHelper::url($item->pdf_path) }}" target="_blank" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 4px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-file-pdf"></i> {{ __('دوسية') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    <button type="button" 
                                            id="vis_btn_{{ $item->id }}" 
                                            onclick="toggleVideographerVisibility({{ $item->id }}, this)" 
                                            class="vis-toggle-pill {{ $isVis ? 'is-visible' : 'is-hidden' }}" 
                                            title="{{ $isVis ? __('انقر لقفل وحجب المحاضرة فوراً عن الطلاب') : __('انقر لإتاحة وعرض المحاضرة فوراً للطلاب') }}">
                                        <i class="fa-solid {{ $isVis ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        <span id="vis_txt_{{ $item->id }}">{{ $isVis ? __('متاح للطلبة') : __('محجوب عن الطلبة') }}</span>
                                    </button>
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-weight: 600; font-size: 0.84rem;">
                                    <i class="fa-regular fa-eye" style="margin-left: 4px;"></i> {{ number_format($item->views_count) }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 0.8rem;">
                                    {{ $item->created_at ? $item->created_at->format('Y/m/d') : '-' }}
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <!-- زر تبديل الإتاحة والحجب السريع -->
                                        <button type="button" 
                                                id="quick_vis_{{ $item->id }}"
                                                onclick="toggleVideographerVisibility({{ $item->id }}, document.getElementById('vis_btn_{{ $item->id }}'))" 
                                                style="background: {{ $isVis ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $isVis ? '#a7f3d0' : '#fecaca' }}; color: {{ $isVis ? '#059669' : '#dc2626' }}; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" 
                                                title="{{ $isVis ? __('حجب الفيديو عن الطلاب') : __('إتاحة وعرض الفيديو للطلاب') }}">
                                            <i class="fa-solid {{ $isVis ? 'fa-eye' : 'fa-eye-slash' }}" style="font-size: 0.85rem;"></i>
                                        </button>

                                        <a href="{{ route('educational_contents.edit', $item->id) }}" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0284c7; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; text-decoration: none; transition: all 0.15s;" title="{{ __('تعديل المحاضرة والملفات') }}">
                                            <i class="fa-regular fa-pen-to-square" style="font-size: 0.85rem;"></i>
                                        </a>

                                        <form action="{{ route('videographer.contents.sync_branches', $item->id) }}" method="POST" style="display: inline-block; margin: 0;" title="{{ __('توزيع ومزامنة هذه المحاضرة تلقائياً على كافة الفروع الأكاديمية الشقيقة') }}">
                                            @csrf
                                            <button type="submit" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" title="{{ __('مزامنة وتوزيع لكافة الفروع') }}">
                                                <i class="fa-solid fa-layer-group" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </form>

                                        <form id="delete_form_video_{{ $item->id }}" action="{{ route('videographer.contents.destroy', $item->id) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="deleteVideographerContent({{ $item->id }}, '{{ addslashes($item->title) }}')" style="background: none; border: 1px solid #fee2e2; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: background 0.15s;" title="{{ __('حذف المحاضرة') }}">
                                                <i class="fa-regular fa-trash-can" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- الترقيم والتنقل بين الصفحات --}}
            @if($contents->hasPages())
                <div style="padding: 18px 24px; border-top: 1px solid var(--ed-border); background: #fafafa; display: flex; justify-content: center;">
                    {{ $contents->links() }}
                </div>
            @endif
        @endif

    </div>

</div>

{{-- نافذة منبثقة لمعاينة الفيديو (Video Preview Modal) --}}
<div id="videoPreviewModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 16px; max-width: 800px; width: 100%; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); animation: modalFadeIn 0.2s ease;">
        <div style="padding: 16px 20px; background: #0f172a; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <strong id="previewModalTitle" style="font-size: 0.95rem; font-family: 'Alexandria', sans-serif;">{{ __('معاينة المحاضرة') }}</strong>
            <button type="button" onclick="closeVideoPreview()" style="background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div style="background: #000000; position: relative; padding-top: 56.25%; width: 100%;">
            <video id="previewPlayerVideo" controls style="display: none; position: absolute; top:0; left:0; width: 100%; height: 100%;" src=""></video>
            <iframe id="previewPlayerIframe" style="display: none; position: absolute; top:0; left:0; width: 100%; height: 100%; border: none;" src="" allowfullscreen></iframe>
        </div>
        <div style="padding: 14px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeVideoPreview()" style="background: #e2e8f0; color: #334155; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                {{ __('إغلاق') }}
            </button>
        </div>
    </div>
</div>

<script>
    function openVideoPreview(title, url) {
        const modal = document.getElementById('videoPreviewModal');
        const titleEl = document.getElementById('previewModalTitle');
        const videoEl = document.getElementById('previewPlayerVideo');
        const iframeEl = document.getElementById('previewPlayerIframe');

        titleEl.textContent = title;

        if (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com')) {
            let embedUrl = url;
            if (url.includes('watch?v=')) {
                embedUrl = url.replace('watch?v=', 'embed/');
            } else if (url.includes('youtu.be/')) {
                embedUrl = url.replace('youtu.be/', 'www.youtube.com/embed/');
            }
            iframeEl.src = embedUrl;
            iframeEl.style.display = 'block';
            videoEl.style.display = 'none';
        } else {
            videoEl.src = url;
            videoEl.style.display = 'block';
            iframeEl.style.display = 'none';
        }

        modal.style.display = 'grid';
    }

    function closeVideoPreview() {
        const modal = document.getElementById('videoPreviewModal');
        const videoEl = document.getElementById('previewPlayerVideo');
        const iframeEl = document.getElementById('previewPlayerIframe');

        if (videoEl) {
            videoEl.pause();
            videoEl.src = '';
        }
        if (iframeEl) {
            iframeEl.src = '';
        }
        modal.style.display = 'none';
    }

    // إغلاق المعاينة عند النقر خارج النافذة
    document.getElementById('videoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoPreview();
        }
    });

    function deleteVideographerContent(id, title) {
        const doDelete = () => {
            const row = document.getElementById('content_row_' + id);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0.3';
            }
            fetch('/videographer/contents/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'DELETE',
                    'Accept': 'application/json'
                }
            }).then(res => res.json().catch(() => ({})))
            .then(data => {
                if (row) {
                    row.remove();
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف بنجاح ✅',
                        text: data.message || 'تم حذف المحاضرة نهائياً من كافة الفروع الأكاديمية.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).catch(() => {
                const form = document.getElementById('delete_form_video_' + id);
                if (form) form.submit();
            });
        };

        if (window.Swal) {
            Swal.fire({
                title: 'هل أنت متأكد من الحذف؟',
                text: 'سيتم حذف المحاضرة "' + title + '" وجميع نسخها من كافة الفروع الأكاديمية.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، احذف الآن',
                cancelButtonText: 'إلغاء'
            }).then(result => {
                if (result.isConfirmed) {
                    doDelete();
                }
            });
        } else {
            if (confirm('هل أنت متأكد من حذف هذه المحاضرة نهائياً؟')) {
                doDelete();
            }
        }
    }

    async function toggleVideographerVisibility(id, btn) {
        const pillBtn = btn || document.getElementById('vis_btn_' + id);
        const quickBtn = document.getElementById('quick_vis_' + id);
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
                if (quickBtn) {
                    quickBtn.style.background = isVis ? '#ecfdf5' : '#fef2f2';
                    quickBtn.style.borderColor = isVis ? '#a7f3d0' : '#fecaca';
                    quickBtn.style.color = isVis ? '#059669' : '#dc2626';
                    quickBtn.title = isVis ? '{{ __("حجب الفيديو عن الطلاب") }}' : '{{ __("إتاحة وعرض الفيديو للطلاب") }}';
                    quickBtn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-eye-slash'}" style="font-size: 0.85rem;"></i>`;
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
            } else {
                alert(err.message || 'تعذر تحديث حالة العرض.');
            }
        }
    }

    function purgeAllVideographerContents() {
        if (window.Swal) {
            Swal.fire({
                title: '⚠️ تحذير: حذف وتصفير كافة المحاضرات نهائياً!',
                text: 'هل أنت متأكد من رغبتك بحذف كافة المحاضرات والدوسيات الخاصة بك؟ سيتم مسحها نهائياً من قاعدة البيانات والسيرفر ومن كافة الفروع الأكاديمية دون إمكانية استرجاعها!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، حذف وتصفير الكل فوراً',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'جاري حذف وتصفير المحاضرات...',
                        text: 'يرجى الانتظار لحظات لمسح السجلات والملفات من السيرفر...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('{{ url("/videographer/contents/purge-all") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-HTTP-Method-Override': 'DELETE',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف والتصفير بنجاح ✅',
                                text: data.message || 'تم حذف وتصفير كافة المحتويات من قاعدة البيانات.',
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'تعذر الحذف!',
                                text: data.message || 'حدث خطأ أثناء محاولة التصفير.',
                                confirmButtonText: 'موافق'
                            });
                        }
                    }).catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في الاتصال',
                            text: 'تعذر إتمام طلب الحذف، يرجى إعادة المحاولة.',
                            confirmButtonText: 'موافق'
                        });
                    });
                }
            });
        } else {
            if (confirm('هل أنت متأكد من حذف وتصفير كافة محاضراتك نهائياً من قاعدة البيانات والسيرفر؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url("/videographer/contents/purge-all") }}';
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
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
