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
                <span>{{ __('مكتبة المحاضرات المجمعة') }}</span>
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 700; color: var(--ed-text-main); margin: 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-film" style="color: #1d4ed8;"></i>
                {{ __('مكتبة وسجل المحاضرات المصورة المرفوعة') }}
            </h1>
            <p style="margin: 4px 0 0 0; font-size: 0.84rem; color: #64748b;">
                {{ __('يظهر كل درس بسجل مستقل ومرتب، مع إمكانية عرض تفاصيل فروعه وحالة نشره بنقرة واحدة.') }}
            </p>
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
        <form action="{{ route('videographer.contents.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) auto; gap: 14px; align-items: end;">
            
            {{-- تصفية بالفرع الأكاديمي --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-graduation-cap" style="color: #2563eb; margin-left: 4px;"></i> {{ __('الفرع الأكاديمي') }}
                </label>
                <select name="stage_id" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الفروع') }}</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
                            {{ $stage->short_label ?? $stage->label_ar ?? $stage->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- تصفية بالمادة الدراسية --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-book-open" style="color: #2563eb; margin-left: 4px;"></i> {{ __('المادة الدراسية') }}
                </label>
                <select name="subject_id" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ __('جميع المواد') }}</option>
                    @if(isset($uniqueSubjects))
                        @foreach($uniqueSubjects as $sub)
                            @php
                                $cleanSubTitle = $sub->clean_name ?? trim(preg_replace('/\s*\(.*?\)\s*/u', '', $sub->name_ar ?? $sub->name));
                            @endphp
                            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                {{ $cleanSubTitle }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- البحث بالنص --}}
            <div style="min-width: 200px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-magnifying-glass" style="color: #64748b; margin-left: 4px;"></i> {{ __('بحث باسم المحاضرة أو المادة') }}
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث عن عنوان المحاضرة أو اسم المادة...') }}" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; box-sizing: border-box;">
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

            {{-- تصفية الجمهور والمنطقة --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-map-location-dot" style="color: #64748b; margin-left: 4px;"></i> {{ __('الجمهور المستهدف') }}
                </label>
                <select name="target_region" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ __('كافة الشروحات 🌐') }}</option>
                    <option value="gaza" {{ request('target_region') == 'gaza' ? 'selected' : '' }}>🌿 {{ __('قطاع غزة') }}</option>
                    <option value="west_bank" {{ request('target_region') == 'west_bank' ? 'selected' : '' }}>🏛️ {{ __('الضفة والقدس') }}</option>
                    <option value="all" {{ request('target_region') == 'all' ? 'selected' : '' }}>🌐 {{ __('منهاج مشترك') }}</option>
                </select>
            </div>

            {{-- أزرار التصفية وإعادة الضبط --}}
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #1d4ed8; color: #ffffff; border: none; height: 42px; padding: 0 18px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-filter"></i>
                    <span>{{ __('تصفية') }}</span>
                </button>
                @if(request()->hasAny(['stage_id', 'subject_id', 'search', 'visibility', 'target_region']))
                    <a href="{{ route('videographer.contents.index') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; height: 42px; padding: 0 14px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="{{ __('إلغاء الفلتر') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- 3. قائمة المحاضرات المجمعة --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); box-shadow: var(--ed-shadow-sm); overflow: hidden;">
        
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--ed-border); background: #fafafa; display: flex; align-items: center; justify-content: space-between;">
            <strong style="color: var(--ed-text-main); font-size: 0.95rem;">
                <i class="fa-solid fa-folder-tree" style="color: #1d4ed8; margin-left: 6px;"></i>
                {{ __('إجمالي الدروس المسجلة:') }} <span style="color: #1d4ed8; font-weight: 800;">{{ $contents->total() }}</span> {{ __('درس مستقل') }}
            </strong>
            <span style="font-size: 0.82rem; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 20px;">
                <i class="fa-solid fa-sparkles text-primary" style="margin-left: 4px;"></i>{{ __('منظمة ومجمعة حسب المحاضرة دون تكرار') }}
            </span>
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
            <div class="table-responsive">
                <table style="width: 100%; min-width: 820px; border-collapse: collapse; text-align: right; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--ed-border); color: #475569; font-weight: 600; font-size: 0.8rem;">
                            <th style="padding: 14px 20px; width: 50px;">#</th>
                            <th style="padding: 14px 16px;">{{ __('الدرس / المحاضرة') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الفروع والمواد المنشور بها') }}</th>
                            <th style="padding: 14px 16px;">{{ __('الملفات والمرفقات') }}</th>
                            <th style="padding: 14px 16px; text-align: center;">{{ __('حالة العرض للطلاب') }}</th>
                            <th style="padding: 14px 16px;">{{ __('إجمالي المشاهدات') }}</th>
                            <th style="padding: 14px 16px;">{{ __('تاريخ الرفع') }}</th>
                            <th style="padding: 14px 20px; text-align: center;">{{ __('معاينة / إدارة') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contents as $index => $item)
                            @php
                                $isAllVis = $item->all_visible;
                                $isAnyVis = $item->any_visible;
                                $encodedLesson = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8');
                                $cleanUrl = trim((string)($item->url_path ?? ''));
                                $isBadUrl = empty($cleanUrl) || in_array(strtolower($cleanUrl), ['undefined', 'null', 'none', 'false', '0', 'undefined.mp4', 'null.mp4']);
                                $streamUrl = !$isBadUrl ? \App\Support\MediaHelper::videoStreamUrl($cleanUrl) : ($item->youtube_embed_url ?? null);
                                $ytUrl = $item->youtube_embed_url ?? '';
                                $pdfUrl = $item->pdf_path ? \App\Support\MediaHelper::url($item->pdf_path) : '';
                            @endphp
                            <tr id="lesson_row_{{ $item->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 20px; color: #94a3b8; font-weight: 600; font-size: 0.8rem;">
                                    {{ $contents->firstItem() + $index }}
                                </td>
                                
                                {{-- اسم الدرس وعنوانه مع قابلية النقر لتشغيل الفيديو --}}
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div onclick="openVideoPreview('{{ addslashes($item->title) }}', '{{ $streamUrl }}', '{{ $ytUrl }}', '{{ $pdfUrl }}', {{ $item->id }})" 
                                             style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0; cursor: pointer; transition: all 0.2s;" 
                                             onmouseover="this.style.background='#1d4ed8'; this.style.color='#ffffff';" 
                                             onmouseout="this.style.background='#eff6ff'; this.style.color='#1d4ed8';" 
                                             title="{{ $streamUrl ? __('انقر لتشغيل ومعاينة الفيديو فوراً') : __('انقر لمعاينة حالة الفيديو') }}">
                                            <i class="fa-solid fa-play"></i>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" onclick="openLessonModal({{ $encodedLesson }})" style="color: var(--ed-text-main); font-weight: 700; font-size: 0.92rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: color 0.15s; line-height: 1.4;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='var(--ed-text-main)'">
                                                <span>{{ $item->title }}</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.72rem; color: #94a3b8;"></i>
                                            </a>
                                            <div style="display: flex; gap: 8px; align-items: center; font-size: 0.74rem; color: #64748b; margin-top: 4px; flex-wrap: wrap;">
                                                @if($item->channel_name)
                                                    <span><i class="fa-solid fa-chalkboard-user"></i> {{ $item->channel_name }}</span>
                                                @endif
                                                @if($item->file_size)
                                                    <span>• <i class="fa-solid fa-hard-drive"></i> {{ $item->file_size }}</span>
                                                @endif
                                                <span onclick="openLessonModal({{ $encodedLesson }})" style="display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 7px; border-radius: 6px; font-weight: 700; cursor: pointer;" title="{{ __('انقر لعرض الأماكن التي نزل فيها الدرس بالفروع') }}">
                                                    <i class="fa-solid fa-layer-group"></i> <span id="row_branch_count_{{ $item->id }}">{{ $item->branches_count }}</span> {{ __('فروع') }}
                                                </span>
                                                @if(!empty($item->target_region_badge))
                                                    <span style="display: inline-flex; align-items: center; gap: 4px; background: {{ $item->target_region_badge['bg'] }}; color: {{ $item->target_region_badge['color'] }}; border: 1px solid {{ $item->target_region_badge['border'] }}; padding: 1px 7px; border-radius: 6px; font-weight: 700; font-size: 0.72rem;">
                                                        <i class="{{ $item->target_region_badge['icon'] }}"></i> {{ $item->target_region_badge['label'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- الفروع والمواد المنشور بها الدرس --}}
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                                            @foreach(collect($item->branches)->take(3) as $br)
                                                <span onclick="openLessonModal({{ $encodedLesson }})" style="cursor: pointer; display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; padding: 3px 8px; border-radius: 6px; font-size: 0.76rem; font-weight: 600; transition: all 0.15s;" onmouseover="this.style.borderColor='#1d4ed8'; this.style.color='#1d4ed8';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#334155';">
                                                    <span style="font-weight: 700; color: #1d4ed8;">{{ $br['stage_badge'] }}</span>: {{ $br['subject_name'] }}
                                                </span>
                                            @endforeach
                                            @if($item->branches_count > 3)
                                                <span onclick="openLessonModal({{ $encodedLesson }})" style="cursor: pointer; background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; font-size: 0.74rem; font-weight: 700;">
                                                    +{{ $item->branches_count - 3 }} {{ __('فروع أخرى') }}
                                                </span>
                                            @endif
                                        </div>
                                        <button type="button" onclick="openLessonModal({{ $encodedLesson }})" style="background: none; border: none; padding: 0; color: #2563eb; font-size: 0.76rem; font-weight: 700; cursor: pointer; text-align: right; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle-nodes"></i>
                                            <span>{{ __('عرض تفاصيل أماكن التوزيع...') }}</span>
                                        </button>
                                    </div>
                                </td>

                                {{-- الملف والمرفقات مع تشغيل مباشر وحالة واضحة --}}
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                        @if($streamUrl)
                                            <button type="button" 
                                                    data-title="{{ $item->title }}" 
                                                    data-url="{{ $streamUrl }}" 
                                                    data-yt="{{ $ytUrl }}"
                                                    data-pdf="{{ $pdfUrl }}"
                                                    onclick="openVideoPreview(this.getAttribute('data-title'), this.getAttribute('data-url'), this.getAttribute('data-yt'), this.getAttribute('data-pdf'), {{ $item->id }})" 
                                                    style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 4px 9px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-eye"></i> {{ __('معاينة') }}
                                            </button>
                                        @else
                                            <button type="button" 
                                                    onclick="openVideoPreview('{{ addslashes($item->title) }}', '', '', '{{ $pdfUrl }}', {{ $item->id }})" 
                                                    style="background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; padding: 4px 9px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                                    title="{{ __('لا يوجد فيديو مسجل - انقر للتفاصيل والتعديل') }}">
                                                <i class="fa-solid fa-video-slash"></i> {{ __('بدون فيديو') }}
                                            </button>
                                        @endif
                                        @if($item->pdf_path)
                                            <a href="{{ \App\Support\MediaHelper::url($item->pdf_path) }}" target="_blank" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 4px 9px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-file-pdf"></i> {{ __('دوسية') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                {{-- حالة العرض للطلاب عبر كل الفروع --}}
                                <td style="padding: 14px 16px; text-align: center;">
                                    <button type="button" 
                                            id="vis_btn_{{ $item->id }}" 
                                            onclick="toggleVideographerVisibility({{ $item->id }}, this)" 
                                            class="vis-toggle-pill {{ $isAllVis ? 'is-visible' : ($isAnyVis ? 'is-partial' : 'is-hidden') }}" 
                                            title="{{ $isAllVis ? __('انقر لقفل وحجب المحاضرة عن كافة الفروع') : __('انقر لإتاحة وعرض المحاضرة لكافة الفروع') }}">
                                        <i class="fa-solid {{ $isAllVis ? 'fa-eye' : ($isAnyVis ? 'fa-eye-slash' : 'fa-lock') }}"></i>
                                        <span id="vis_txt_{{ $item->id }}">
                                            @if($isAllVis)
                                                {{ __('متاح للطلبة') }}
                                            @elseif($isAnyVis)
                                                {{ __('متاح جزئياً') }}
                                            @else
                                                {{ __('محجوب عن الطلبة') }}
                                            @endif
                                        </span>
                                    </button>
                                </td>

                                {{-- إجمالي المشاهدات في كل الفروع --}}
                                <td style="padding: 14px 16px; color: #64748b; font-weight: 600; font-size: 0.84rem;">
                                    <i class="fa-regular fa-eye" style="margin-left: 4px;"></i> {{ number_format($item->total_views) }}
                                </td>

                                {{-- تاريخ الرفع --}}
                                <td style="padding: 14px 16px; color: #64748b; font-size: 0.8rem;">
                                    {{ $item->created_at ? $item->created_at->format('Y/m/d') : '-' }}
                                </td>

                                {{-- الإجراءات الموحدة --}}
                                <td style="padding: 14px 20px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <!-- زر فتح نافذة تفاصيل الفروع والتوزيع -->
                                        <button type="button" 
                                                onclick="openLessonModal({{ $encodedLesson }})" 
                                                style="background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" 
                                                title="{{ __('فتح تفاصيل أماكن التوزيع بالفروع') }}">
                                            <i class="fa-solid fa-layer-group" style="font-size: 0.9rem;"></i>
                                        </button>

                                        <!-- زر تعديل المحاضرة والملفات -->
                                        <a href="{{ route('videographer.contents.edit', $item->id) }}" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0284c7; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; text-decoration: none; transition: all 0.15s;" title="{{ __('تعديل المحاضرة والملفات') }}">
                                            <i class="fa-regular fa-pen-to-square" style="font-size: 0.9rem;"></i>
                                        </a>

                                        <!-- زر مزامنة وتوزيع إضافي لكافة الفروع -->
                                        <button type="button" 
                                                id="sync_btn_{{ $item->id }}"
                                                onclick="syncLessonBranches({{ $item->id }}, this)" 
                                                style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" 
                                                title="{{ __('مزامنة وتوزيع لكافة الفروع الأكاديمية الشقيقة') }}">
                                            <i class="fa-solid fa-arrows-rotate" style="font-size: 0.85rem;"></i>
                                        </button>

                                        <!-- زر حذف الدرس بالكامل من كافة الفروع -->
                                        <button type="button" onclick="deleteVideographerContent({{ $item->id }}, '{{ addslashes($item->title) }}', 'all')" style="background: none; border: 1px solid #fee2e2; color: #dc2626; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: background 0.15s;" title="{{ __('حذف الدرس بالكامل من كافة الفروع') }}">
                                            <i class="fa-regular fa-trash-can" style="font-size: 0.9rem;"></i>
                                        </button>
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

{{-- 4. النافذة المنبثقة المصممة خصيصاً لتفاصيل أماكن نشر وتوزيع الدرس بالفروع --}}
<div id="lessonDistributionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); z-index: 99990; place-items: center; padding: 20px; overflow-y: auto;">
    <div style="background: #ffffff; border-radius: 20px; max-width: 780px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(226, 232, 240, 0.8); animation: modalScaleIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);">
        
        {{-- هيدر المودال الأنيق مع البيانات الأساسية --}}
        <div style="padding: 24px 28px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; position: relative;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; display: grid; place-items: center; font-size: 1.3rem; flex-shrink: 0;">
                        <i class="fa-solid fa-film"></i>
                    </div>
                    <div>
                        <span style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #93c5fd; padding: 2px 10px; border-radius: 20px; font-size: 0.74rem; font-weight: 700; margin-bottom: 6px;">
                            {{ __('تفاصيل الدرس وتوزيع الفروع') }}
                        </span>
                        <h2 id="dist_modal_title" style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin: 0; font-family: 'Alexandria', sans-serif; line-height: 1.4;">
                            {{ __('عنوان الدرس') }}
                        </h2>
                    </div>
                </div>

                <button type="button" onclick="closeLessonModal()" style="background: rgba(255, 255, 255, 0.1); border: none; color: #94a3b8; width: 36px; height: 36px; border-radius: 10px; font-size: 1.1rem; cursor: pointer; display: grid; place-items: center; transition: all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#94a3b8';">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- شريط الشارات والمعلومات السريعة --}}
            <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-top: 18px; font-size: 0.8rem; color: #cbd5e1; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 14px;">
                <span id="dist_modal_channel"><i class="fa-solid fa-chalkboard-user text-primary" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_size"><i class="fa-solid fa-hard-drive text-success" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_date"><i class="fa-regular fa-calendar" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_views"><i class="fa-regular fa-eye text-warning" style="margin-left: 5px;"></i> <span>0 مشاهدة</span></span>
                <span style="margin-inline-start: auto; background: #2563eb; color: #fff; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.76rem;" id="dist_modal_branches_badge">
                    <i class="fa-solid fa-layer-group"></i> 3 فروع
                </span>
            </div>
        </div>

        {{-- جسم المودال: بطاقات الأماكن والفروع --}}
        <div style="padding: 24px 28px; background: #f8fafc; max-height: 60vh; overflow-y: auto;">
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; font-family: 'Alexandria', sans-serif;">
                    <i class="fa-solid fa-diagram-project" style="color: #2563eb;"></i>
                    {{ __('الأماكن والفروع الأكاديمية التي نزل فيها هذا الدرس:') }}
                </h3>
                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">
                    {{ __('تحكم بحالة العرض لكل فرع باستقلالية') }}
                </span>
            </div>

            {{-- حاوية بطاقات الفروع الديناميكية --}}
            <div id="dist_modal_branches_container" style="display: flex; flex-direction: column; gap: 12px;">
                <!-- يتم حقن بطاقات الفروع هنا بالجافاسكريبت -->
            </div>

        </div>

        {{-- شريط أزرار التحكم السفلية للمودال --}}
        <div style="padding: 16px 28px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <button type="button" id="dist_modal_btn_preview" onclick="" style="background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-play"></i> {{ __('معاينة الفيديو') }}
                </button>

                <a id="dist_modal_btn_pdf" href="#" target="_blank" style="display: none; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #059669; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; text-decoration: none; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-file-pdf"></i> {{ __('فتح الدوسية PDF') }}
                </a>

                <a id="dist_modal_btn_edit" href="#" style="background: #f8fafc; border: 1.5px solid #cbd5e1; color: #334155; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-regular fa-pen-to-square"></i> {{ __('تعديل المحاضرة') }}
                </a>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <button type="button" id="dist_modal_btn_delete_all" onclick="" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-trash-can"></i> {{ __('حذف من كافة الفروع') }}
                </button>

                <button type="button" onclick="closeLessonModal()" style="background: #e2e8f0; color: #334155; border: none; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                    {{ __('إغلاق') }}
                </button>
            </div>
        </div>

    </div>
</div>

{{-- نافذة منبثقة لمعاينة الفيديو الذكية (Video Preview Modal) --}}
<div id="videoPreviewModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.88); backdrop-filter: blur(12px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #0f172a; border-radius: 18px; max-width: 920px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7); border: 1px solid rgba(255,255,255,0.12); animation: modalScaleIn 0.2s ease;">
        <div style="padding: 14px 20px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; color: #fff; font-size: 0.95rem; font-weight: 700; overflow: hidden; min-width: 0;">
                <i class="fa-solid fa-play" style="color: #60a5fa; flex-shrink: 0;"></i>
                <span id="preview_video_title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ __('معاينة المحاضرة') }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <a id="preview_video_external_link" href="#" target="_blank" rel="noopener noreferrer" style="display: none; align-items: center; gap: 6px; color: #93c5fd; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; text-decoration: none;" title="{{ __('فتح الفيديو في تبويب خارجي مستقل') }}">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>{{ __('فتح في تبويب خارجي') }}</span>
                </a>
                <button type="button" onclick="closeVideoPreview()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center;" title="{{ __('إغلاق المعاينة') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div style="position: relative; padding-top: 56.25%; background: #000; overflow: hidden;">
            {{-- مشغل الفيديو المباشر لملفات MP4 / WebM وبث الخادم --}}
            <video id="preview_video_player" controls playsinline controlsList="nodownload" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; display: none;">
                <source id="preview_video_source" src="" type="video/mp4">
            </video>

            {{-- مشغل Iframe لفيديوهات يوتيوب وفيميو والروابط الخارجية المضمنة --}}
            <iframe id="preview_video_iframe" 
                    style="position: absolute; inset: 0; width: 100%; height: 100%; border: none; display: none;" 
                    allowfullscreen 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
            </iframe>

            {{-- شاشة بديلة ذكية في حال تعثر التشغيل أو عدم توفر ملف --}}
            <div id="preview_video_error" style="display: none; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.95); flex-direction: column; align-items: center; justify-content: center; gap: 14px; padding: 24px; text-align: center; color: #fff; z-index: 10;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 id="preview_error_h4" style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: #f8fafc;">{{ __('تعذر تشغيل الفيديو داخل هذا المشغل المباشر') }}</h4>
                    <p id="preview_error_p" style="margin: 0; font-size: 0.82rem; color: #94a3b8; max-width: 460px; line-height: 1.5;">{{ __('قد يكون مصدر الفيديو محمي أو غير متوافق مع العرض المضمن. بإمكانك فتح الرابط مباشرة في نافذة جديدة أو تعديل الدرس لإرفاق الفيديو.') }}</p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 6px; flex-wrap: wrap; justify-content: center;">
                    <a id="preview_video_error_link" href="#" target="_blank" rel="noopener noreferrer" style="display: none; background: #2563eb; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>{{ __('فتح الفيديو في تبويب جديد') }}</span>
                    </a>
                    <a id="preview_video_error_edit" href="#" style="display: inline-flex; background: #3b82f6; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; align-items: center; gap: 6px;">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span>{{ __('تعديل الدرس وإرفاق الفيديو') }}</span>
                    </a>
                    <a id="preview_video_error_pdf" href="#" target="_blank" rel="noopener noreferrer" style="display: none; background: #059669; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>{{ __('عرض دوسية وملزمة الدرس PDF') }}</span>
                    </a>
                    <button type="button" onclick="closeVideoPreview()" style="background: rgba(255,255,255,0.1); color: #cbd5e1; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                        {{ __('إغلاق') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let activeLessonData = null;

    // 1. فتح نافذة تفاصيل الدرس وتوزيع الفروع
    function openLessonModal(lesson) {
        activeLessonData = lesson;
        const modal = document.getElementById('lessonDistributionModal');
        
        // تعبئة البيانات الأساسية في الهيدر
        document.getElementById('dist_modal_title').textContent = lesson.title || '{{ __("محاضرة تعليمية") }}';
        
        const channelSpan = document.getElementById('dist_modal_channel').querySelector('span');
        if (channelSpan) channelSpan.textContent = lesson.channel_name || '{{ __("المصور الأكاديمي") }}';
        
        const sizeSpan = document.getElementById('dist_modal_size').querySelector('span');
        if (sizeSpan) sizeSpan.textContent = lesson.file_size || '{{ __("فيديو مرفوع") }}';

        const dateSpan = document.getElementById('dist_modal_date').querySelector('span');
        if (dateSpan) dateSpan.textContent = lesson.created_at ? new Date(lesson.created_at).toLocaleDateString('ar-EG') : '-';

        const viewsSpan = document.getElementById('dist_modal_views').querySelector('span');
        if (viewsSpan) viewsSpan.textContent = (lesson.total_views || 0) + ' {{ __("مشاهدة") }}';

        const badge = document.getElementById('dist_modal_branches_badge');
        if (badge) badge.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${lesson.branches_count} {{ __("فروع أكاديمية") }}`;

        // أزرار المعاينة والدوسية والتعديل
        const btnPreview = document.getElementById('dist_modal_btn_preview');
        if (btnPreview) {
            btnPreview.onclick = () => {
                const rawUrl = lesson.youtube_embed_url || lesson.url_path || '';
                let streamUrl = rawUrl;
                if (rawUrl && !rawUrl.startsWith('http') && !rawUrl.includes('youtube.com') && !rawUrl.includes('youtu.be')) {
                    const clean = rawUrl.replace(/^(storage\/|public\/|\/)/, '');
                    streamUrl = '/video-stream/' + clean;
                }
                openVideoPreview(lesson.title, streamUrl, lesson.youtube_embed_url, lesson.pdf_path ? ('/storage/' + lesson.pdf_path) : null, lesson.id);
            };
        }

        const btnPdf = document.getElementById('dist_modal_btn_pdf');
        if (btnPdf) {
            if (lesson.pdf_path) {
                btnPdf.href = '/storage/' + lesson.pdf_path;
                btnPdf.style.display = 'inline-flex';
            } else {
                btnPdf.style.display = 'none';
            }
        }

        const btnEdit = document.getElementById('dist_modal_btn_edit');
        if (btnEdit) {
            btnEdit.href = '/videographer/contents/' + lesson.id + '/edit';
        }

        const btnDeleteAll = document.getElementById('dist_modal_btn_delete_all');
        if (btnDeleteAll) {
            btnDeleteAll.onclick = () => {
                deleteVideographerContent(lesson.id, lesson.title, 'all');
            };
        }

        // بناء قائمة بطاقات الفروع
        renderBranchesContainer(lesson);

        modal.style.display = 'grid';
        document.body.style.overflow = 'hidden';
    }

    function renderBranchesContainer(lesson) {
        const container = document.getElementById('dist_modal_branches_container');
        container.innerHTML = '';

        if (!lesson.branches || lesson.branches.length === 0) {
            container.innerHTML = `
                <div style="background: #ffffff; border: 1.5px dashed #cbd5e1; border-radius: 12px; padding: 24px; text-align: center; color: #64748b;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 1.5rem; color: #94a3b8; margin-bottom: 8px;"></i>
                    <p style="margin: 0; font-size: 0.9rem; font-weight: 600;">{{ __("لم يتم العثور على أي فروع مرتبطة بهذا الدرس") }}</p>
                </div>
            `;
            return;
        }

        lesson.branches.forEach((branch, idx) => {
            const isVis = Boolean(branch.is_visible);
            
            // تحديد أيقونة ولون مخصص لكل فرع
            let branchIcon = 'fa-graduation-cap';
            let branchBg = '#eff6ff';
            let branchColor = '#1d4ed8';

            if (branch.stage_name.includes('علمي')) {
                branchIcon = 'fa-atom';
                branchBg = '#e0f2fe';
                branchColor = '#0284c7';
            } else if (branch.stage_name.includes('أدبي')) {
                branchIcon = 'fa-book';
                branchBg = '#f0fdf4';
                branchColor = '#16a34a';
            } else if (branch.stage_name.includes('ريادة') || branch.stage_name.includes('أعمال') || branch.stage_name.includes('تجاري')) {
                branchIcon = 'fa-briefcase';
                branchBg = '#fff7ed';
                branchColor = '#ea580c';
            } else if (branch.stage_name.includes('شرعي')) {
                branchIcon = 'fa-scale-balanced';
                branchBg = '#faf5ff';
                branchColor = '#9333ea';
            } else if (branch.stage_name.includes('صناعي')) {
                branchIcon = 'fa-gears';
                branchBg = '#f1f5f9';
                branchColor = '#475569';
            }

            const card = document.createElement('div');
            card.id = `modal_branch_card_${branch.id}`;
            card.style.cssText = 'background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.03);';
            card.onmouseover = () => { card.style.borderColor = '#93c5fd'; card.style.boxShadow = '0 4px 12px rgba(37,99,235,0.08)'; };
            card.onmouseout = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 1px 3px rgba(0,0,0,0.03)'; };

            card.innerHTML = `
                <div style="display: flex; align-items: center; gap: 14px; min-width: 0;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: ${branchBg}; color: ${branchColor}; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0;">
                        <i class="fa-solid ${branchIcon}"></i>
                    </div>
                    <div style="min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <strong style="font-size: 0.95rem; color: #0f172a; font-weight: 800; font-family: 'Alexandria', sans-serif;">
                                ${branch.stage_name}
                            </strong>
                            <span style="background: ${branchBg}; color: ${branchColor}; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 800;">
                                ${branch.stage_badge}
                            </span>
                        </div>
                        <div style="font-size: 0.82rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span><i class="fa-solid fa-book-open" style="color: #3b82f6; margin-left: 4px;"></i> {{ __('المادة:') }} <b style="color: #1e293b;">${branch.subject_name}</b></span>
                            <span>•</span>
                            <span><i class="fa-regular fa-eye" style="margin-left: 4px;"></i> ${branch.views_count} {{ __('مشاهدة') }}</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                    <!-- زر تبديل حالة الإتاحة لهذا الفرع منفرداً -->
                    <button type="button" 
                            id="branch_vis_btn_${branch.id}"
                            onclick="toggleSingleBranchVisibility(${branch.id}, this)"
                            class="vis-toggle-pill ${isVis ? 'is-visible' : 'is-hidden'}"
                            title="${isVis ? '{{ __("انقر لقفل وحجب المحاضرة عن هذا الفرع") }}' : '{{ __("انقر لإتاحة وعرض المحاضرة لهذا الفرع") }}'}">
                        <i class="fa-solid ${isVis ? 'fa-eye' : 'fa-lock'}"></i>
                        <span>${isVis ? '{{ __("متاح للفرع") }}' : '{{ __("محجوب عن الفرع") }}'}</span>
                    </button>

                    <!-- زر إزالة المحاضرة من هذا الفرع فقط -->
                    <button type="button" 
                            onclick="deleteSingleBranch(${branch.id}, '${escapeJsString(branch.stage_name)}')"
                            style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" 
                            title="{{ __('إزالة الدرس من هذا الفرع فقط') }}">
                        <i class="fa-regular fa-trash-can" style="font-size: 0.85rem;"></i>
                    </button>
                </div>
            `;

            container.appendChild(card);
        });
    }

    function escapeJsString(str) {
        return (str || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function closeLessonModal() {
        const modal = document.getElementById('lessonDistributionModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // إغلاق المودال عند النقر خارج المحتوى
    document.getElementById('lessonDistributionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLessonModal();
        }
    });

    // 2. تبديل حالة العرض لفرع واحد فقط من داخل النافذة
    async function toggleSingleBranchVisibility(branchId, btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(`/videographer/contents/${branchId}/toggle-visibility?scope=single`, {
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
                btn.className = 'vis-toggle-pill ' + (isVis ? 'is-visible' : 'is-hidden');
                btn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-lock'}"></i><span>${isVis ? '{{ __("متاح للفرع") }}' : '{{ __("محجوب عن الفرع") }}'}</span>`;
                btn.disabled = false;

                // تحديث الذاكرة المحلية
                if (activeLessonData && activeLessonData.branches) {
                    const br = activeLessonData.branches.find(b => b.id == branchId);
                    if (br) br.is_visible = isVis;
                }

                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
                        icon: 'success',
                        title: data.message || '{{ __("تم تحديث حالة الفرع بنجاح") }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            } else {
                throw new Error(data.error || 'حدث خطأ');
            }
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: err.message || 'تعذر التحديث.' });
            }
        }
    }

    // 3. حذف الدرس من فرع واحد فقط من داخل النافذة
    function deleteSingleBranch(branchId, stageName) {
        if (!window.Swal) {
            if (!confirm(`هل تريد إزالة هذا الدرس من (${stageName}) فقط؟`)) return;
        }

        Swal.fire({
            title: '{{ __("إزالة من هذا الفرع فقط؟") }}',
            text: `{{ __("سيتم حذف الدرس من فرع") }} (${stageName}) {{ __("فقط، وسيبقى متاحاً للطلاب في باقي الفروع.") }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("نعم، احذفه من هذا الفرع") }}',
            cancelButtonText: '{{ __("تراجع") }}'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/videographer/contents/${branchId}?scope=single`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json().catch(() => ({})))
                .then(data => {
                    const card = document.getElementById(`modal_branch_card_${branchId}`);
                    if (card) card.remove();

                    // تحديث قائمة الفروع في الكائن النشط
                    if (activeLessonData && activeLessonData.branches) {
                        activeLessonData.branches = activeLessonData.branches.filter(b => b.id != branchId);
                        activeLessonData.branches_count = activeLessonData.branches.length;
                        
                        // تحديث الشارة في المودال
                        const badge = document.getElementById('dist_modal_branches_badge');
                        if (badge) badge.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${activeLessonData.branches_count} {{ __("فروع أكاديمية") }}`;

                        // تحديث العداد في سطر الجدول
                        const rowCount = document.getElementById(`row_branch_count_${activeLessonData.id}`);
                        if (rowCount) rowCount.textContent = activeLessonData.branches_count;

                        // إذا لم يتبق أي فروع، نحذف السطر ونغلق المودال
                        if (activeLessonData.branches_count === 0) {
                            const row = document.getElementById(`lesson_row_${activeLessonData.id}`);
                            if (row) row.remove();
                            closeLessonModal();
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("تمت الإزالة بنجاح") }}',
                        text: `{{ __("تمت إزالة الدرس من فرع") }} (${stageName}) {{ __("بنجاح.") }}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            }
        });
    }

    // 4. حذف الدرس بالكامل من كافة الفروع
    function deleteVideographerContent(id, title, scope = 'all') {
        const doDelete = () => {
            const row = document.getElementById('lesson_row_' + id);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0.3';
            }
            closeLessonModal();

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
                        timer: 2200,
                        showConfirmButton: false
                    });
                }
            }).catch(() => {
                location.reload();
            });
        };

        if (window.Swal) {
            Swal.fire({
                title: '{{ __("هل أنت متأكد من حذف هذا الدرس؟") }}',
                text: '{{ __("سيتم حذف المحاضرة") }} "' + title + '" {{ __("ونهائياً من كافة الفروع الأكاديمية وقاعدة البيانات.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '{{ __("نعم، احذف من كافة الفروع") }}',
                cancelButtonText: '{{ __("إلغاء") }}'
            }).then(result => {
                if (result.isConfirmed) {
                    doDelete();
                }
            });
        } else {
            if (confirm('هل أنت متأكد من حذف هذه المحاضرة نهائياً من كافة الفروع؟')) {
                doDelete();
            }
        }
    }

    // 5. تبديل حالة العرض لكافة الفروع من الجدول الرئيسي
    async function toggleVideographerVisibility(id, btn) {
        const pillBtn = btn || document.getElementById('vis_btn_' + id);
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
                    pillBtn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-lock'}"></i><span>${isVis ? '{{ __("متاح للطلبة") }}' : '{{ __("محجوب عن الطلبة") }}'}</span>`;
                    pillBtn.disabled = false;
                }

                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
                        icon: 'success',
                        title: data.message || '{{ __("تم تحديث حالة العرض لكافة الفروع بنجاح") }}',
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

    // 6. مشغل معاينة الفيديو
    function openVideoPreview(title, url, ytUrl = null, pdfUrl = null, lessonId = null) {
        const modal = document.getElementById('videoPreviewModal');
        const player = document.getElementById('preview_video_player');
        const source = document.getElementById('preview_video_source');
        const iframe = document.getElementById('preview_video_iframe');
        const titleEl = document.getElementById('preview_video_title');
        const extLink = document.getElementById('preview_video_external_link');
        const errorBox = document.getElementById('preview_video_error');
        const errorLink = document.getElementById('preview_video_error_link');
        const errorPdf = document.getElementById('preview_video_error_pdf');
        const errorEdit = document.getElementById('preview_video_error_edit');
        const errorH4 = document.getElementById('preview_error_h4');
        const errorP = document.getElementById('preview_error_p');

        if (titleEl) titleEl.textContent = title || '{{ __("معاينة المحاضرة") }}';

        if (errorEdit) {
            if (lessonId) {
                errorEdit.href = '/videographer/contents/' + lessonId + '/edit';
                errorEdit.style.display = 'inline-flex';
            } else {
                errorEdit.style.display = 'none';
            }
        }

        const trimmedUrl = String(url || ytUrl || '').trim();
        const invalidTokens = ['undefined', 'null', 'none', 'false', '0', '#', 'undefined.mp4', 'null.mp4'];
        const isCorrupted = !trimmedUrl || invalidTokens.includes(trimmedUrl.toLowerCase()) || trimmedUrl.endsWith('/undefined') || trimmedUrl.endsWith('/null');

        if (extLink) {
            if (!isCorrupted) {
                extLink.href = trimmedUrl;
                extLink.style.display = 'inline-flex';
            } else {
                extLink.style.display = 'none';
            }
        }
        if (errorLink) {
            if (!isCorrupted) {
                errorLink.href = trimmedUrl;
                errorLink.style.display = 'inline-flex';
            } else {
                errorLink.style.display = 'none';
            }
        }
        if (errorPdf) {
            if (pdfUrl) {
                errorPdf.href = pdfUrl;
                errorPdf.style.display = 'inline-flex';
            } else {
                errorPdf.style.display = 'none';
            }
        }
        if (errorBox) {
            errorBox.style.display = 'none';
        }

        if (isCorrupted) {
            if (player) {
                player.pause();
                player.removeAttribute('src');
                if (source) source.removeAttribute('src');
                player.style.display = 'none';
            }
            if (iframe) {
                iframe.src = 'about:blank';
                iframe.style.display = 'none';
            }
            if (errorBox) errorBox.style.display = 'flex';
            if (errorH4) errorH4.textContent = 'لم يتم إرفاق ملف فيديو لهذا الدرس بعد ⚠️';
            if (errorP) errorP.textContent = 'هذا الدرس مسجل حالياً بدون ملف فيديو مرفوع أو رابط يوتيوب صالح. بإمكانك الضغط على زر تعديل الدرس لإرفاق الفيديو أو وضع رابط يوتيوب.';
            if (modal) modal.style.display = 'grid';
            document.body.style.overflow = 'hidden';
            return;
        }

        const isYouTube = /(?:youtube\.com\/(?:watch\?|embed\/|shorts\/|live\/)|youtu\.be\/)/i.test(trimmedUrl);
        const isVimeo = /vimeo\.com/i.test(trimmedUrl);

        if (isYouTube || isVimeo) {
            if (player) {
                player.pause();
                player.removeAttribute('src');
                if (source) source.removeAttribute('src');
                player.style.display = 'none';
            }

            let embedUrl = trimmedUrl;
            if (isYouTube) {
                const ym = trimmedUrl.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([a-zA-Z0-9_\-]{11})/i);
                if (ym && ym[1]) {
                    embedUrl = 'https://www.youtube.com/embed/' + ym[1] + '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
                } else if (trimmedUrl.includes('watch?v=')) {
                    embedUrl = trimmedUrl.replace('watch?v=', 'embed/') + (trimmedUrl.includes('?') ? '&' : '?') + 'autoplay=1&rel=0';
                }
            } else if (isVimeo) {
                const vm = trimmedUrl.match(/vimeo\.com\/(?:video\/)?([0-9]+)/i);
                if (vm && vm[1]) {
                    embedUrl = 'https://player.vimeo.com/video/' + vm[1] + '?autoplay=1';
                }
            }

            if (iframe) {
                iframe.src = embedUrl;
                iframe.style.display = 'block';
            }
        } else {
            if (iframe) {
                iframe.src = 'about:blank';
                iframe.style.display = 'none';
            }

            if (player) {
                player.style.display = 'block';
                player.pause();
                player.src = trimmedUrl;
                if (source) source.src = trimmedUrl;

                player.onerror = function() {
                    if (ytUrl && ytUrl !== trimmedUrl) {
                        openVideoPreview(title, ytUrl, null, pdfUrl, lessonId);
                        return;
                    }
                    if (errorBox) errorBox.style.display = 'flex';
                    fetch(trimmedUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
                        .then(res => {
                            if (res.status === 422) {
                                return res.json().then(data => {
                                    if (errorH4) errorH4.textContent = 'ملف هذا الدرس غير مكتمل في التخزين (0 بايت)';
                                    if (errorP) errorP.textContent = data.message || 'هذا الفيديو تم حفظه مسبقاً بحجم 0 بايت أو لم يكتمل دمجه. بإمكانك إعادة رفع الفيديو عبر خيار التعديل.';
                                });
                            }
                        })
                        .catch(() => {});
                };

                player.load();
                player.play().catch(() => {});
            }
        }

        if (modal) modal.style.display = 'grid';
        document.body.style.overflow = 'hidden';
    }

    function closeVideoPreview() {
        const modal = document.getElementById('videoPreviewModal');
        const player = document.getElementById('preview_video_player');
        const source = document.getElementById('preview_video_source');
        const iframe = document.getElementById('preview_video_iframe');
        const errorBox = document.getElementById('preview_video_error');

        if (player) {
            player.pause();
            player.removeAttribute('src');
            if (source) source.removeAttribute('src');
            player.load();
            player.style.display = 'none';
        }
        if (iframe) {
            iframe.src = 'about:blank';
            iframe.style.display = 'none';
        }
        if (errorBox) errorBox.style.display = 'none';
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // 7. مزامنة وتوزيع المحاضرة على كافة الفروع الأكاديمية الشقيقة بلمسة واحدة
    async function syncLessonBranches(id, btn) {
        const targetBtn = btn || document.getElementById('sync_btn_' + id);
        const origHtml = targetBtn ? targetBtn.innerHTML : '';
        if (targetBtn) {
            targetBtn.disabled = true;
            targetBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="font-size: 0.85rem;"></i>';
        }

        try {
            const response = await fetch('/videographer/contents/' + id + '/sync-branches', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();

            if (targetBtn) {
                targetBtn.disabled = false;
                targetBtn.innerHTML = origHtml;
            }

            if (data.success) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت المزامنة بنجاح 🎉',
                        text: data.message || 'تم توزيع ومزامنة المحاضرة على كافة الفروع الأكاديمية المطابقة.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert(data.message || 'تمت المزامنة بنجاح');
                    location.reload();
                }
            } else {
                throw new Error(data.error || 'حدث خطأ أثناء المزامنة');
            }
        } catch (err) {
            if (targetBtn) {
                targetBtn.disabled = false;
                targetBtn.innerHTML = origHtml;
            }
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في المزامنة',
                    text: err.message || 'تعذر استكمال المزامنة حالياً.'
                });
            } else {
                alert('خطأ: ' + err.message);
            }
        }
    }

    document.getElementById('videoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoPreview();
        }
    });

    // إغلاق أي نافذة بزر الهروب Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLessonModal();
            closeVideoPreview();
        }
    });

    // 7. حذف وتصفير كافة المحتويات
    function purgeAllVideographerContents() {
        if (window.Swal) {
            Swal.fire({
                title: '{{ __("تأكيد تصفير كافة المحتويات نهائياً!") }}',
                text: '{{ __("تحذير فائق الخطورة: هذا الإجراء سيحذف جميع الفيديوهات والمحاضرات والمرفقات الخاصة بك بالكامل، ولا يمكن التراجع عنه مطلقاً!") }}',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '{{ __("نعم، احذف وصفّر كل شيء") }}',
                cancelButtonText: '{{ __("إلغاء وتراجع") }}'
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '{{ __("جاري حذف كافة السجلات والملفات...") }}',
                        text: '{{ __("يرجى الانتظار لحظات حتى اكتمال التصفير...") }}',
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
                    }).then(res => res.json().catch(() => ({})))
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("تم تصفير المحتويات بنجاح!") }}',
                            text: data.message || '{{ __("تم مسح كافة المحاضرات والملفات من قاعدة البيانات والسيرفر.") }}',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }).catch(() => {
                        location.reload();
                    });
                }
            });
        }
    }
</script>

<style>
@keyframes modalScaleIn {
    0% { transform: scale(0.95); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

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

.vis-toggle-pill.is-partial {
    background: #fffbeb;
    color: #92400e;
    border: 1.5px solid #fde68a;
}
.vis-toggle-pill.is-partial:hover {
    background: #fef3c7;
    border-color: #fcd34d;
    transform: translateY(-1px);
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

@media (max-width: 768px) {
    .videographer-library-wrapper {
        padding: 6px 0 60px 0 !important;
    }
    .videographer-library-wrapper form {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
