@extends('layouts.app')

@section('title', __('إدارة ورفع الفيديوهات والشروحات') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="videographer-library-wrapper" style="max-width: 1300px; margin: 0 auto; padding: 10px 0 60px 0;">

    {{-- 1. رأس الصفحة والمسار الأكاديمي --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 0.82rem; color: var(--ed-text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('teacher.dashboard') }}" style="color: #1d4ed8; text-decoration: none; font-weight: 700;">
                    {{ auth()->user()->role === 'admin' ? __('لوحة الإدارة') : __('بوابة المعلم المعتمد') }}
                </a>
                <i class="fa-solid fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size: 0.7rem;"></i>
                <span>{{ __('إدارة ورفع الفيديوهات والشروحات') }}</span>
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--ed-text-main); margin: 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-video" style="color: #1d4ed8;"></i>
                {{ __('إدارة ورفع الفيديوهات والشروحات المرئية') }}
            </h1>
            <p style="margin: 4px 0 0 0; font-size: 0.84rem; color: #64748b;">
                {{ __('يظهر كل درس بسجل أكاديمي مستقل ومرتب، مع إمكانية عرض تفاصيل فروعه وحالة نشره ومعاينته بنقرة واحدة دون تكرار أو بطء.') }}
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if(in_array(auth()->user()->role, ['admin', 'teacher']))
                <button type="button" onclick="purgeAllContents()" style="display: inline-flex; align-items: center; gap: 8px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 0.86rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 3px rgba(220, 38, 38, 0.08);" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" title="{{ auth()->user()->role === 'admin' ? __('حذف وتصفير كافة المحتويات دفعة واحدة') : __('حذف وتصفير فيديوهاتي دفعة واحدة') }}">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>{{ auth()->user()->role === 'admin' ? __('تصفير كافة الفيديوهات') : __('تصفير فيديوهاتي') }}</span>
                </button>
            @endif

            <button type="button" onclick="openUploadVideoModal()" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 22px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; border: none; cursor: pointer; transition: background 0.15s; box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>{{ __('إضافة فيديو / شرح جديد') }}</span>
            </button>
        </div>
    </div>

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 12px; padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 0.9rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- تنبيه ذكي في حال وجود فيديو مكتمل الرفع على الخادم بانتظار النشر --}}
    @if(!empty($recentUnlinkedVideo))
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border: 1.5px solid #86efac; border-radius: 14px; padding: 16px 22px; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.12); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #22c55e; color: #ffffff; display: grid; place-items: center; font-size: 1.3rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <strong style="font-size: 0.96rem; color: #14532d; font-family: 'Alexandria', sans-serif;">
                            {{ __('تم اكتشاف فيديو مكتمل الرفع بحجم ') }} ({{ $recentUnlinkedVideo['size'] }}) {{ __('بانتظار إكمال النشر!') }}
                        </strong>
                        <span style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 99px; border: 1px solid #bbf7d0;">
                            {{ __('جاهز للنشر') }}
                        </span>
                    </div>
                    <span style="font-size: 0.8rem; color: #166534; display: block; margin-top: 3px;">
                        {{ __('الملف:') }} <code style="background: rgba(255,255,255,0.85); padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #0f172a;">{{ $recentUnlinkedVideo['filename'] }}</code> • {{ __('رُفع:') }} {{ $recentUnlinkedVideo['time_ago'] }}
                    </span>
                </div>
            </div>

            <button type="button" onclick="openUploadVideoModal()" style="background: #16a34a; color: #ffffff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.86rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 3px 8px rgba(22, 163, 74, 0.25);">
                <i class="fa-solid fa-bolt"></i> {{ __('إكمال النشر والتوزيع الآن') }}
            </button>
        </div>
    @endif

    {{-- شريط الإحصائيات والمؤشرات السريعة --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #eff6ff; color: #1e40af; display: grid; place-items: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="fa-solid fa-film"></i>
            </div>
            <div>
                <span style="display: block; font-size: 1.45rem; font-weight: 900; color: #0f172a; line-height: 1.2;">{{ $stats['total'] ?? $videos->total() }}</span>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">{{ __('إجمالي الدروس المستقلة') }}</span>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #ecfdf5; color: #059669; display: grid; place-items: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="display: block; font-size: 1.45rem; font-weight: 900; color: #059669; line-height: 1.2;">{{ $stats['visible'] ?? 0 }}</span>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">{{ __('شروحات معروضة للطلبة') }}</span>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #fffbeb; color: #d97706; display: grid; place-items: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="fa-solid fa-eye-slash"></i>
            </div>
            <div>
                <span style="display: block; font-size: 1.45rem; font-weight: 900; color: #d97706; line-height: 1.2;">{{ $stats['hidden'] ?? 0 }}</span>
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">{{ __('شروحات محجوبة أو جزئية') }}</span>
            </div>
        </div>
    </div>

    {{-- 2. شريط البحث والتصفية المنظم الموحد (مثل صفحة المصور تماماً وبدون أزرار عائمة مبعثرة) --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 18px 24px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm);">
        @php
            $filterRoute = auth()->user()->role === 'admin' ? route('admin.videos') : route('teacher.videos');
        @endphp
        <form action="{{ $filterRoute }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) auto; gap: 14px; align-items: end;">
            
            {{-- تصفية بالفرع الأكاديمي --}}
            @if(isset($stages) && count($stages) > 0)
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
            @endif

            {{-- تصفية بالمادة الدراسية بقائمة منسقة ومنظمة بدون تكرار --}}
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-book-open" style="color: #2563eb; margin-left: 4px;"></i> {{ __('المادة الدراسية') }}
                </label>
                <select name="subject_id" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; background: #fff;" onchange="this.form.submit()">
                    <option value="">{{ auth()->user()->role === 'admin' ? __('جميع المواد') : __('جميع موادي') }}</option>
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
            <div style="min-width: 220px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    <i class="fa-solid fa-magnifying-glass" style="color: #64748b; margin-left: 4px;"></i> {{ __('بحث بعنوان المحاضرة أو المعلم') }}
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث عن عنوان الدرس أو اسم المعلم...') }}" class="uni-input" style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; color: #0f172a; box-sizing: border-box;">
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
                    <a href="{{ $filterRoute }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; height: 42px; padding: 0 14px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="{{ __('إلغاء الفلتر') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- 3. قائمة المحاضرات المجمعة في جدول أكاديمي موحد --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); box-shadow: var(--ed-shadow-sm); overflow: hidden;">
        
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--ed-border); background: #fafafa; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <strong style="color: var(--ed-text-main); font-size: 0.95rem;">
                <i class="fa-solid fa-folder-tree" style="color: #1d4ed8; margin-left: 6px;"></i>
                {{ __('إجمالي الدروس المسجلة:') }} <span style="color: #1d4ed8; font-weight: 800;">{{ $videos->total() }}</span> {{ __('درس مستقل') }}
            </strong>
            <span style="font-size: 0.82rem; color: #64748b; background: #f1f5f9; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                <i class="fa-solid fa-sparkles text-primary" style="margin-left: 4px;"></i>{{ __('منظمة ومجمعة حسب المحاضرة دون تكرار أو كروت عائمة') }}
            </span>
        </div>

        @if($videos->isEmpty())
            <div style="padding: 56px 20px; text-align: center; color: var(--ed-text-muted);">
                <div style="width: 68px; height: 68px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto 16px auto; font-size: 2rem; color: #94a3b8;">
                    <i class="fa-solid fa-film"></i>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--ed-text-main); margin: 0 0 6px 0;">{{ __('لم يتم العثور على أي محاضرات أو فيديوهات مطابقة') }}</h3>
                <p style="font-size: 0.86rem; color: var(--ed-text-muted); margin: 0 0 18px 0;">{{ __('جرّب تغيير معايير البحث أو ابدأ برفع درس مرئي جديد الآن') }}</p>
                <button type="button" onclick="openUploadVideoModal()" style="display: inline-flex; align-items: center; gap: 8px; background: #1d4ed8; color: #ffffff; padding: 10px 22px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; border: none; cursor: pointer;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('إضافة درس جديد') }}</span>
                </button>
            </div>
        @else
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table style="width: 100%; min-width: 840px; border-collapse: collapse; text-align: right; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--ed-border); color: #475569; font-weight: 700; font-size: 0.8rem;">
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
                        @foreach($videos as $index => $item)
                            @php
                                $isAllVis = $item->all_visible;
                                $isAnyVis = $item->any_visible;
                                $encodedLesson = htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8');
                                $streamUrl = $item->url_path ? \App\Support\MediaHelper::videoStreamUrl($item->url_path) : ($item->youtube_embed_url ?? null);
                            @endphp
                            <tr id="lesson_row_{{ $item->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 14px 20px; color: #94a3b8; font-weight: 700; font-size: 0.8rem;">
                                    {{ $videos->firstItem() + $index }}
                                </td>
                                
                                {{-- اسم الدرس وعنوانه مع قابلية النقر لفتح نافذة الفروع --}}
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div onclick="openLessonModal({{ $encodedLesson }})" style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#1d4ed8'; this.style.color='#ffffff';" onmouseout="this.style.background='#eff6ff'; this.style.color='#1d4ed8';" title="{{ __('انقر لفتح تفاصيل توزيع الفروع') }}">
                                            <i class="fa-solid fa-play"></i>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" onclick="openLessonModal({{ $encodedLesson }})" style="color: var(--ed-text-main); font-weight: 800; font-size: 0.92rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: color 0.15s; line-height: 1.4;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='var(--ed-text-main)'">
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
                                                <span onclick="openLessonModal({{ $encodedLesson }})" style="display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 7px; border-radius: 6px; font-weight: 700; cursor: pointer;" title="{{ __('الأماكن التي نزل فيها الدرس بالفروع') }}">
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

                                {{-- الملف والمرفقات ومعاينة الفيديو السريعة بدون تشغيل تلقائي --}}
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                        @if($streamUrl || !empty($item->youtube_embed_url) || !empty($item->url_path))
                                            <button type="button" 
                                                    data-title="{{ $item->title }}" 
                                                    data-url="{{ $streamUrl ?? $item->url_path ?? '' }}" 
                                                    data-yt="{{ $item->youtube_watch_url ?? $item->youtube_embed_url ?? '' }}"
                                                    data-pdf="{{ $item->pdf_path ? \App\Support\MediaHelper::url($item->pdf_path) : '' }}"
                                                    onclick="openVideoPreview(this.getAttribute('data-title'), this.getAttribute('data-url'), this.getAttribute('data-yt'), this.getAttribute('data-pdf'))" 
                                                    style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-eye"></i> {{ __('معاينة') }}
                                            </button>
                                        @endif
                                        @if($item->pdf_path)
                                            <a href="{{ \App\Support\MediaHelper::url($item->pdf_path) }}" target="_blank" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-file-pdf"></i> {{ __('دوسية') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                {{-- حالة العرض للطلاب عبر كل الفروع --}}
                                <td style="padding: 14px 16px; text-align: center;">
                                    <button type="button" 
                                            id="vis_btn_{{ $item->id }}" 
                                            onclick="toggleUnifiedVisibility({{ $item->id }}, this)" 
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
                                        @php
                                            $editRoute = auth()->user()->role === 'admin' 
                                                ? route('admin.educational_contents.edit', $item->id) 
                                                : route('teacher.educational_contents.edit', $item->id);
                                        @endphp
                                        <a href="{{ $editRoute }}" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0284c7; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; text-decoration: none; transition: all 0.15s;" title="{{ __('تعديل المحاضرة والملفات') }}">
                                            <i class="fa-regular fa-pen-to-square" style="font-size: 0.9rem;"></i>
                                        </a>

                                        <!-- زر مزامنة وتوزيع إضافي لكافة الفروع -->
                                        <form action="{{ auth()->user()->role === 'admin' ? route('admin.contents.sync_branches', $item->id) : route('teacher.contents.sync_branches', $item->id) }}" method="POST" style="display: inline-block; margin: 0;" title="{{ __('توزيع ومزامنة هذه المحاضرة تلقائياً على كافة الفروع الأكاديمية الشقيقة') }}">
                                            @csrf
                                            <button type="submit" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: all 0.15s;" title="{{ __('مزامنة وتوزيع لكافة الفروع') }}">
                                                <i class="fa-solid fa-arrows-rotate" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </form>

                                        <!-- زر حذف الدرس بالكامل من كافة الفروع -->
                                        <button type="button" onclick="deleteUnifiedContent({{ $item->id }}, '{{ addslashes($item->title) }}', 'all')" style="background: none; border: 1px solid #fee2e2; color: #dc2626; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: inline-grid; place-items: center; transition: background 0.15s;" title="{{ __('حذف الدرس بالكامل من كافة الفروع') }}">
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
            @if($videos->hasPages())
                <div style="padding: 18px 24px; border-top: 1px solid var(--ed-border); background: #fafafa; display: flex; justify-content: center;">
                    {{ $videos->links() }}
                </div>
            @endif
        @endif

    </div>

</div>

{{-- 4. النافذة المنبثقة لتفاصيل أماكن نشر وتوزيع الدرس بالفروع --}}
<div id="lessonDistributionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(8px); z-index: 99990; place-items: center; padding: 20px; overflow-y: auto;">
    <div style="background: #ffffff; border-radius: 20px; max-width: 780px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(226, 232, 240, 0.8); animation: modalScaleIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);">
        
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

            <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-top: 18px; font-size: 0.8rem; color: #cbd5e1; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 14px;">
                <span id="dist_modal_channel"><i class="fa-solid fa-chalkboard-user text-primary" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_size"><i class="fa-solid fa-hard-drive text-success" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_date"><i class="fa-regular fa-calendar" style="margin-left: 5px;"></i> <span>-</span></span>
                <span>•</span>
                <span id="dist_modal_views"><i class="fa-regular fa-eye text-warning" style="margin-left: 5px;"></i> <span>0 مشاهدة</span></span>
                <span style="margin-inline-start: auto; background: #2563eb; color: #fff; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.76rem;" id="dist_modal_branches_badge">
                    <i class="fa-solid fa-layer-group"></i> 0 فروع أكاديمية
                </span>
            </div>
        </div>

        <div style="padding: 24px 28px; max-height: 52vh; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h4 style="font-size: 0.92rem; font-weight: 700; color: #334155; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-diagram-project" style="color: #2563eb;"></i>
                    {{ __('قائمة الفروع الأكاديمية التي يظهر بها هذا الدرس:') }}
                </h4>
                <span style="font-size: 0.75rem; color: #64748b;">
                    {{ __('يمكنك إتاحة أو حجب الدرس لكل فرع على حدة') }}
                </span>
            </div>

            <div id="dist_modal_branches_list" style="display: flex; flex-direction: column; gap: 12px;"></div>
        </div>

        <div style="padding: 18px 28px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" id="dist_modal_preview_btn" style="background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-play"></i> {{ __('معاينة الفيديو') }}
                </button>
                <a id="dist_modal_pdf_link" href="#" target="_blank" style="display: none; background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #059669; padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 0.84rem; text-decoration: none; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-file-pdf"></i> {{ __('تحميل الدوسية PDF') }}
                </a>
            </div>

            <div style="display: flex; align-items: center; gap: 10px;">
                <button type="button" id="dist_modal_delete_all_btn" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626; padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 0.84rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-trash-can"></i> {{ __('حذف من كافة الفروع') }}
                </button>
                <button type="button" onclick="closeLessonModal()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 20px; border-radius: 10px; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                    {{ __('إغلاق') }}
                </button>
            </div>
        </div>

    </div>
</div>

{{-- 5. نافذة معاينة الفيديو السريعة بدون استهلاك مسبق للبيانات --}}
<div id="videoPreviewModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.88); backdrop-filter: blur(12px); z-index: 99999; place-items: center; padding: 20px;">
    <div style="background: #0f172a; border-radius: 18px; max-width: 920px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7); border: 1px solid rgba(255,255,255,0.12);">
        <div style="padding: 14px 20px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; color: #fff; font-size: 0.95rem; font-weight: 700; overflow: hidden; min-width: 0;">
                <i class="fa-solid fa-play" style="color: #60a5fa; flex-shrink: 0;"></i>
                <span id="preview_video_title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ __('معاينة الفيديو') }}</span>
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
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin">
            </iframe>

            {{-- شاشة بديلة ذكية في حال تعثر التشغيل --}}
            <div id="preview_video_error" style="display: none; position: absolute; inset: 0; background: rgba(15, 23, 42, 0.95); flex-direction: column; align-items: center; justify-content: center; gap: 14px; padding: 24px; text-align: center; color: #fff; z-index: 10;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 id="preview_error_h4" style="margin: 0 0 6px; font-size: 1rem; font-weight: 700; color: #f8fafc;">{{ __('تعذر تشغيل الفيديو داخل هذا المشغل المباشر') }}</h4>
                    <p id="preview_error_p" style="margin: 0; font-size: 0.82rem; color: #94a3b8; max-width: 460px; line-height: 1.5;">{{ __('قد يكون مصدر الفيديو محمي أو غير متوافق مع العرض المضمن. بإمكانك فتح الرابط مباشرة في نافذة جديدة.') }}</p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 6px; flex-wrap: wrap; justify-content: center;">
                    <a id="preview_video_error_link" href="#" target="_blank" rel="noopener noreferrer" style="background: #2563eb; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>{{ __('فتح الفيديو في تبويب جديد') }}</span>
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

{{-- 6. نافذة Modal إضافة فيديو جديد بتقنية التجزئة وفروع المواد المتعددة --}}
<div id="uploadVideoModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3>
                <div class="modal-head-icon">
                    <i class="fa-solid fa-film"></i>
                </div>
                <span>{{ __('إضافة درس أو شرح مرئي جديد') }}</span>
            </h3>
            <button type="button" onclick="closeUploadVideoModal()" class="btn-close-modal" title="{{ __('إغلاق النافذة') }}">&times;</button>
        </div>

        <form id="uploadVideoForm" onsubmit="submitVideoForm(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="video">

            <div class="modal-form-body">
                @if(auth()->user()->role === 'admin' && isset($stages) && count($stages) > 0)
                    <!-- خانات اختيار الفروع والمادة المشتركة للمدير العام بتصميم أكاديمي ملكي منظم -->
                    <div class="branch-selector-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                            <label class="f-label" style="margin: 0; font-weight: 800; color: #1e3a8a; font-size: 0.9rem;">
                                <i class="fa-solid fa-layer-group" style="color: #2563eb;"></i> {{ __('الفروع الأكاديمية المستهدفة:') }}
                            </label>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" onclick="selectAllModalBranches('video', true)" class="btn-branch-util">{{ __('تحديد الكل') }}</button>
                                <button type="button" onclick="selectAllModalBranches('video', false)" class="btn-branch-util btn-branch-util-clear">{{ __('إلغاء التحديد') }}</button>
                            </div>
                        </div>

                        <div class="branch-cards-grid">
                            @foreach($stages as $stage)
                                @php
                                    $shortName = $stage->short_label ?? $stage->label_ar;
                                    $icon = $stage->icon ?? '🎓';
                                @endphp
                                <label class="branch-select-card" id="v_stage_card_{{ $stage->id }}">
                                    <input type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" class="video-modal-stage-check" onchange="onVideoModalSelectionChange()" checked>
                                    <div class="branch-card-content">
                                        <span class="branch-icon">{{ $icon }}</span>
                                        <span class="branch-name">{{ $shortName }}</span>
                                        <i class="fa-solid fa-circle-check branch-check-icon"></i>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- المادة المشتركة المختارة -->
                        <div style="margin-top: 14px;">
                            <label class="f-label" style="font-weight: 700; color: #1e3a8a; font-size: 0.88rem; margin-bottom: 6px;">
                                <i class="fa-solid fa-book-open" style="color: #2563eb;"></i> {{ __('المادة الدراسية المشتركة / المبحث:') }}
                            </label>
                            <select id="video_admin_subject_select" name="subject_id" class="f-control" required onchange="onVideoModalSelectionChange()" style="min-height: 48px; line-height: 1.6;">
                                <option value="">{{ __('اختر المادة الدراسية (مثال: اللغة العربية، اللغة الإنجليزية...)...') }}</option>
                                @php
                                    $uniqueSubjectsList = collect($subjects)->unique('clean_name');
                                @endphp
                                @foreach($uniqueSubjectsList as $uSub)
                                    <option value="{{ $uSub->id }}" data-clean-name="{{ $uSub->clean_name }}" data-key="{{ $uSub->subject_key }}">
                                        {{ $uSub->clean_name }}
                                    </option>
                                @endforeach
                                <option disabled>────────── فروع المواد التفصيلية ──────────</option>
                                @foreach($subjects as $sub)
                                    @php
                                        $subStageName = $sub->stage ? ($sub->stage->short_label ?? $sub->stage->label_ar) : 'عام';
                                    @endphp
                                    <option value="{{ $sub->id }}" data-clean-name="{{ $sub->clean_name }}" data-key="{{ $sub->subject_key }}">
                                        {{ $sub->clean_name }} — {{ $subStageName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- تنبيه الفروع والمواد المستهدفة بالتوازي -->
                        <div id="videoPublishTargetAlert" style="display: none; margin-top: 12px; padding: 12px 14px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px;">
                            <div style="color: #166534; font-size: 0.84rem; font-weight: 800; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-check-double" style="color: #15803d;"></i>
                                <span>{{ __('سيتم نشر هذا الدرس وتوفيره للمدرسين والطلبة بالتوازي في:') }}</span>
                            </div>
                            <div id="videoSelectedSubjectsList" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                        </div>
                        <div id="videoHiddenSubjectIdsWrap"></div>
                    </div>
                @else
                    <!-- المادة الدراسية للمعلم -->
                    <div class="f-group">
                        <label class="f-label">{{ __('المادة الدراسية والمرحلة *') }}</label>
                        <select name="subject_id" required class="f-control">
                            <option value="">{{ __('اختر المادة الدراسية...') }}</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ (isset($subjectId) && $subjectId == $sub->id) ? 'selected' : '' }}>
                                    {{ $sub->name_ar ?? $sub->name }} {{ optional($sub->stage)->label_ar ? ' - (' . optional($sub->stage)->label_ar . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- عنوان الفيديو -->
                <div class="f-group">
                    <label class="f-label">{{ __('عنوان الدرس / الشرح المرئي *') }}</label>
                    <input type="text" name="title" required placeholder="{{ __('مثال: شرح الوحدة الأولى - الدرس الأول: القوانين الأساسية') }}" class="f-control">
                </div>

                <!-- خيار توجيه الفيديو (غزة / الضفة / كلاهما) -->
                <div class="f-group">
                    <label class="f-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            <i class="fa-solid fa-map-location-dot" style="color: #0284c7;"></i>
                            {{ __('الفئة المستهدفة من الطلبة *') }}
                        </span>
                        <span style="font-size: 0.76rem; color: #64748b;">
                            {{ __('يحدد من يشاهد هذا الفيديو في حسابه') }}
                        </span>
                    </label>
                    <div class="region-select-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-top: 4px;">
                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="gaza" style="display: none;" onchange="updateVideoRegionSelect(this)">
                            <div class="region-pill-box" id="v_card_gaza" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🌿</div>
                                <strong style="display: block; font-size: 0.85rem; color: #065f46;">{{ __('قطاع غزة') }}</strong>
                                <span style="font-size: 0.7rem; color: #64748b;">{{ __('لطلبة غزة فقط') }}</span>
                            </div>
                        </label>

                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="west_bank" style="display: none;" onchange="updateVideoRegionSelect(this)">
                            <div class="region-pill-box" id="v_card_west_bank" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🏛️</div>
                                <strong style="display: block; font-size: 0.85rem; color: #1e40af;">{{ __('الضفة والقدس') }}</strong>
                                <span style="font-size: 0.7rem; color: #64748b;">{{ __('لطلبة الضفة فقط') }}</span>
                            </div>
                        </label>

                        <label style="cursor: pointer; margin: 0;">
                            <input type="radio" name="target_region" value="all" checked style="display: none;" onchange="updateVideoRegionSelect(this)">
                            <div class="region-pill-box active" id="v_card_all" style="border: 2px solid #2563eb; border-radius: 12px; padding: 10px 8px; text-align: center; transition: all 0.2s ease; background: #eff6ff;">
                                <div style="font-size: 1.25rem; margin-bottom: 2px;">🌐</div>
                                <strong style="display: block; font-size: 0.85rem; color: #1e3a8a;">{{ __('منهاج مشترك') }}</strong>
                                <span style="font-size: 0.7rem; color: #3b82f6;">{{ __('لكافة طلبة الوطن') }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- خيار 1: إرفاق رابط يوتيوب غير مدرج فوري ومحمي بدون حدود للحجم -->
                <div class="f-group" style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;">
                    <label class="f-label" style="font-weight: 800; color: #1e3a8a; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                        <span>
                            <i class="fa-brands fa-youtube" style="color: #dc2626; font-size: 1.15rem;"></i>
                            {{ __('رابط فيديو YouTube غير مدرج (Unlisted) 🚀') }}
                        </span>
                        <span style="font-size: 0.74rem; font-weight: 800; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                            {{ __('مساحة غير محدودة وبدون ضغط') }}
                        </span>
                    </label>
                    <input type="text" name="video_url" id="modalYtUrlInput" placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/..." class="f-control" style="border-color: #93c5fd; background: #ffffff; padding: 11px 14px; font-size: 0.9rem;">
                    <small style="color: #475569; font-weight: 600; display: block; margin-top: 6px; font-size: 0.78rem;">
                        <i class="fa-solid fa-shield-halved" style="color: #2563eb;"></i> {{ __('يُعرض داخل المنصة بمشغل محمي تلقائياً خالي من الإعلانات، وبدون أزرار مشاركة أو روابط خارجية.') }}
                    </small>
                </div>

                <!-- فاصل أنيق بين اليوتيوب والرفع المحلي -->
                <div style="display: flex; align-items: center; margin: 14px 0; gap: 10px;">
                    <hr style="flex: 1; border: none; border-top: 1px dashed #cbd5e1; margin: 0;">
                    <span style="font-size: 0.76rem; font-weight: 800; color: #64748b; background: #f8fafc; padding: 3px 12px; border-radius: 999px; border: 1px solid #e2e8f0;">
                        {{ __('أو رفع ملف فيديو MP4 محلي من جهازك') }}
                    </span>
                    <hr style="flex: 1; border: none; border-top: 1px dashed #cbd5e1; margin: 0;">
                </div>

                <!-- رفع ملف الفيديو محلياً بنظام الأجزاء السريع والآمن -->
                <div class="f-group" id="groupVideoFile">
                    <label class="f-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            <i class="fa-solid fa-cloud-arrow-up" style="color: #2563eb;"></i>
                            {{ __('ملف الفيديو للدرس (MP4 / WebM)') }}
                            <small style="color: #64748b; font-weight: normal; font-size: 0.76rem;">({{ __('اختياري إذا أضفت رابط يوتيوب') }})</small>
                        </span>
                        <span style="font-size: 0.78rem; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                            ⚡ {{ __('رفع محلي فائق السرعة + يدعم الأوفلاين للطلبة') }}
                        </span>
                    </label>

                    <div class="ed-video-dropzone" id="videoDropzone" onclick="document.getElementById('videoFileInput').click()">
                        <input type="file" name="video_file" id="videoFileInput" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-m4v" style="display: none;" onchange="handleVideoFileSelection(this)">
                        <div class="dropzone-inner" id="dropzoneContent">
                            <div class="dropzone-icon">
                                <i class="fa-solid fa-film"></i>
                            </div>
                            <div class="dropzone-text">
                                <strong id="dropzoneTitle">{{ __('اسحب ملف الفيديو وأفلته هنا، أو اضغط للاختيار') }}</strong>
                                <span id="dropzoneSubtitle">{{ __('يدعم ملفات الفيديو بأي حجم مهما كانت ضخمة بالجيجاوات مع التجزئة والاستئناف التلقائي.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- شريط تقدم الرفع المباشر بالأجزاء للملفات الضخمة -->
                    <div id="chunkProgressWrap" style="display: none; margin-top: 14px; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 14px 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span id="chunkProgressStatus" style="font-size: 0.85rem; font-weight: 800; color: #1e40af; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري بدء تجهيز ورفع أجزاء الفيديو...') }}</span>
                            </span>
                            <span id="chunkProgressPct" style="font-size: 0.95rem; font-weight: 900; color: #1e3a8a; font-family: monospace;">0%</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; position: relative;">
                            <div id="chunkProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6, #059669); transition: width 0.2s ease;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 0.76rem; color: #64748b; flex-wrap: wrap; gap: 6px;">
                            <span id="chunkFileMeta" style="font-weight: 700; color: #334155;">-- / --</span>
                            <span id="chunkSpeedMeta" style="font-weight: 700; color: #059669;"><i class="fa-solid fa-gauge-high"></i> --</span>
                            <span id="chunkEtaMeta" style="font-weight: 700; color: #d97706;"><i class="fa-solid fa-clock"></i> --</span>
                            <span id="chunkPartMeta" style="font-weight: 700; color: #64748b;">--</span>
                        </div>
                        <div style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
                            <span style="font-size: 0.76rem; color: #475569;">
                                <i class="fa-solid fa-circle-info" style="color: #2563eb;"></i>
                                {{ __('يمكنك إغلاق هذه النافذة أو تصفح باقي الواجهات بأمان تام دون انقطاع التحميل.') }}
                            </span>
                            <button type="button" onclick="minimizeModalAndBrowse()" style="background: #eff6ff; border: 1.5px solid #93c5fd; color: #1d4ed8; padding: 6px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-window-minimize"></i>
                                <span>{{ __('متابعة الرفع في الخلفية وتصفح المنصة 🚀') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="f-row" style="margin-top: 14px; display: flex; gap: 14px;">
                    <div class="f-group" style="flex: 1;">
                        <label class="f-label">{{ __('اسم المعلم / مقدم الشرح') }}</label>
                        <input type="text" name="channel_name" value="{{ auth()->user()->name_ar ?? auth()->user()->name }}" placeholder="{{ __('مثال: م.أحمد شمالي') }}" class="f-control">
                    </div>

                    <div class="f-group" style="width: 130px;">
                        <label class="f-label">{{ __('ترتيب الدرس') }}</label>
                        <input type="number" name="order" value="1" min="1" class="f-control font-mono">
                    </div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" onclick="closeUploadVideoModal()" class="btn-modal-cancel" id="btnCancelUpload">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSubmitVideo" class="btn-modal-submit">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('بدء رفع ونشر الفيديو') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ==========================================================
   ACADEMIC VIDEO MANAGEMENT & LIBRARY STYLES
   ========================================================== */
.vis-toggle-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    border: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
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
}

@keyframes modalScaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Branch selector in Upload Modal */
.branch-selector-box {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 18px;
}
.btn-branch-util {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.74rem;
    font-weight: 700;
    cursor: pointer;
}
.btn-branch-util-clear {
    color: #dc2626;
    border-color: #fecaca;
}
.branch-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 8px;
}
.branch-select-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
    cursor: pointer;
    display: block;
    transition: all 0.15s;
}
.branch-select-card input {
    display: none;
}
.branch-card-content {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
}
.branch-select-card input:checked + .branch-card-content {
    color: #1d4ed8;
}
.branch-select-card:has(input:checked) {
    border-color: #1d4ed8;
    background: #eff6ff;
}
.branch-check-icon {
    margin-inline-start: auto;
    font-size: 0.85rem;
    color: #cbd5e1;
}
.branch-select-card input:checked ~ .branch-card-content .branch-check-icon {
    color: #1d4ed8;
}

/* Modal Form Controls */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    z-index: 99990;
    place-items: center;
    padding: 20px;
    overflow-y: auto;
}
.modal-card {
    background: #ffffff;
    border-radius: 18px;
    max-width: 680px;
    width: 100%;
    padding: 24px 28px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid #e2e8f0;
}
.modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 18px;
}
.modal-head h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal-head-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
}
.btn-close-modal {
    background: #f1f5f9;
    border: none;
    color: #64748b;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 1.2rem;
    cursor: pointer;
    display: grid;
    place-items: center;
}
.f-group {
    margin-bottom: 16px;
}
.f-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}
.f-control {
    width: 100%;
    height: 42px;
    padding: 0 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.88rem;
    color: #0f172a;
    box-sizing: border-box;
}
.f-control:focus {
    border-color: #2563eb;
    outline: none;
}
.ed-video-dropzone {
    border: 2px dashed #93c5fd;
    border-radius: 12px;
    padding: 26px 20px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.2s;
}
.ed-video-dropzone:hover {
    background: #eff6ff;
    border-color: #3b82f6;
}
.dropzone-icon {
    font-size: 2rem;
    color: #2563eb;
    margin-bottom: 8px;
}
.dropzone-text strong {
    display: block;
    font-size: 0.92rem;
    color: #1e3a8a;
    margin-bottom: 4px;
}
.dropzone-text span {
    font-size: 0.78rem;
    color: #64748b;
}
.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
    margin-top: 20px;
}
.btn-modal-cancel {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
}
.btn-modal-submit {
    background: #1d4ed8;
    color: #ffffff;
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
@media (max-width: 768px) {
    .modal-card {
        padding: 18px 16px;
    }
}
</style>

<script src="{{ asset('js/resumable-uploader.js') }}"></script>

<script>
let activeLessonData = null;

// 1. فتح نافذة تفاصيل توزيع الفروع للمحاضرة
function openLessonModal(lessonData) {
    activeLessonData = lessonData;
    const modal = document.getElementById('lessonDistributionModal');
    if (!modal) return;

    document.getElementById('dist_modal_title').textContent = lessonData.title || '{{ __("عنوان الدرس") }}';
    document.getElementById('dist_modal_channel').innerHTML = `<i class="fa-solid fa-chalkboard-user text-primary" style="margin-left: 5px;"></i> <span>${escapeHtml(lessonData.channel_name || 'عام')}</span>`;
    document.getElementById('dist_modal_size').innerHTML = `<i class="fa-solid fa-hard-drive text-success" style="margin-left: 5px;"></i> <span>${escapeHtml(lessonData.file_size || '--')}</span>`;
    
    let dateStr = '--';
    if (lessonData.created_at) {
        dateStr = lessonData.created_at.substring(0, 10);
    }
    document.getElementById('dist_modal_date').innerHTML = `<i class="fa-regular fa-calendar" style="margin-left: 5px;"></i> <span>${dateStr}</span>`;
    document.getElementById('dist_modal_views').innerHTML = `<i class="fa-regular fa-eye text-warning" style="margin-left: 5px;"></i> <span>${(lessonData.total_views || 0).toLocaleString()} {{ __("مشاهدة") }}</span>`;
    
    const count = (lessonData.branches || []).length;
    document.getElementById('dist_modal_branches_badge').innerHTML = `<i class="fa-solid fa-layer-group"></i> ${count} {{ __("فروع أكاديمية") }}`;

    // زر معاينة الفيديو المباشر
    const prevBtn = document.getElementById('dist_modal_preview_btn');
    const ytSource = lessonData.youtube_watch_url || lessonData.youtube_embed_url || (lessonData.url_path && (lessonData.url_path.includes('youtube') || lessonData.url_path.includes('youtu.be')) ? lessonData.url_path : null);
    const rawVideoTarget = lessonData.url_path || lessonData.youtube_embed_url || lessonData.youtube_watch_url;
    if (rawVideoTarget || ytSource) {
        prevBtn.style.display = 'inline-flex';
        let streamUrl = rawVideoTarget || ytSource;
        const cleanTarget = String(streamUrl).trim();
        if (cleanTarget.includes('youtube') || cleanTarget.includes('youtu.be') || cleanTarget.includes('vimeo.com') || cleanTarget.startsWith('http://') || cleanTarget.startsWith('https://')) {
            streamUrl = cleanTarget;
        } else if (cleanTarget.startsWith('/video-stream/')) {
            streamUrl = cleanTarget;
        } else {
            const cleanPath = cleanTarget.replace(/^(storage\/|public\/|\/)/, '');
            streamUrl = '/video-stream/' + cleanPath;
        }
        const pdfFullUrl = lessonData.pdf_path ? (lessonData.pdf_path.startsWith('http') ? lessonData.pdf_path : '/storage/' + lessonData.pdf_path) : null;
        prevBtn.onclick = () => openVideoPreview(lessonData.title, streamUrl, ytSource, pdfFullUrl);
    } else {
        prevBtn.style.display = 'none';
    }

    // زر تحميل الدوسية PDF
    const pdfLink = document.getElementById('dist_modal_pdf_link');
    if (lessonData.pdf_path) {
        pdfLink.style.display = 'inline-flex';
        pdfLink.href = lessonData.pdf_path.startsWith('http') ? lessonData.pdf_path : '/storage/' + lessonData.pdf_path;
    } else {
        pdfLink.style.display = 'none';
    }

    // زر حذف المحاضرة بالكامل
    document.getElementById('dist_modal_delete_all_btn').onclick = () => {
        deleteUnifiedContent(lessonData.id, lessonData.title, 'all');
    };

    // تعبئة بطاقات الفروع الموزع بها الدرس
    renderModalBranchesList(lessonData.branches || []);

    modal.style.display = 'grid';
    document.body.style.overflow = 'hidden';
}

function renderModalBranchesList(branches) {
    const container = document.getElementById('dist_modal_branches_list');
    container.innerHTML = '';

    if (branches.length === 0) {
        container.innerHTML = `<div style="text-align: center; padding: 24px; color: #94a3b8;">{{ __("لا توجد فروع مسجلة لهذا الدرس.") }}</div>`;
        return;
    }

    branches.forEach(branch => {
        const isVis = Boolean(branch.is_visible);
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
                            ${escapeHtml(branch.stage_name)}
                        </strong>
                        <span style="background: ${branchBg}; color: ${branchColor}; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 800;">
                            ${escapeHtml(branch.stage_badge)}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span><i class="fa-solid fa-book-open" style="color: #3b82f6; margin-left: 4px;"></i> {{ __('المادة:') }} <b style="color: #1e293b;">${escapeHtml(branch.subject_name)}</b></span>
                        <span>•</span>
                        <span><i class="fa-regular fa-eye" style="margin-left: 4px;"></i> ${branch.views_count} {{ __('مشاهدة') }}</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <button type="button" 
                        id="branch_vis_btn_${branch.id}"
                        onclick="toggleSingleBranchVisibility(${branch.id}, this)"
                        class="vis-toggle-pill ${isVis ? 'is-visible' : 'is-hidden'}"
                        title="${isVis ? '{{ __("انقر لقفل وحجب المحاضرة عن هذا الفرع") }}' : '{{ __("انقر لإتاحة وعرض المحاضرة لهذا الفرع") }}'}">
                    <i class="fa-solid ${isVis ? 'fa-eye' : 'fa-lock'}"></i>
                    <span>${isVis ? '{{ __("متاح للفرع") }}' : '{{ __("محجوب عن الفرع") }}'}</span>
                </button>

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

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function escapeJsString(str) {
    return (str || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function closeLessonModal() {
    const modal = document.getElementById('lessonDistributionModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('lessonDistributionModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeLessonModal();
});

// 2. تبديل حالة العرض لفرع واحد فقط
async function toggleSingleBranchVisibility(branchId, btn) {
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const toggleUrl = (isUserAdmin ? '/admin/contents/' : '/teacher/contents/') + branchId + '/toggle-visibility?scope=single';

    try {
        const response = await fetch(toggleUrl, {
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

// 3. تبديل حالة العرض لكافة الفروع بلمسة واحدة
async function toggleUnifiedVisibility(id, btn) {
    if (!id) return;
    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const toggleUrl = (isUserAdmin ? '/admin/visibility/toggle/' : '/teacher/visibility/toggle/') + id;
    const token = '{{ csrf_token() }}';

    btn.disabled = true;
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const res = await fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        const data = await res.json();
        if (data && data.success) {
            const isVis = Boolean(data.is_visible);
            btn.className = 'vis-toggle-pill ' + (isVis ? 'is-visible' : 'is-hidden');
            btn.innerHTML = `<i class="fa-solid ${isVis ? 'fa-eye' : 'fa-lock'}"></i> <span>${isVis ? '{{ __("متاح للطلبة") }}' : '{{ __("محجوب عن الطلبة") }}'}</span>`;
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: '{{ app()->getLocale() == "ar" ? "top-start" : "top-end" }}',
                    icon: 'success',
                    title: data.message || 'تم تحديث حالة الظهور لجميع الفروع',
                    showConfirmButton: false,
                    timer: 1800
                });
            }
        } else {
            throw new Error(data.error || 'تعذر تحديث الظهور');
        }
    } catch (e) {
        btn.innerHTML = origHtml;
        if (window.Swal) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: e.message || 'تعذر تحديث الظهور.' });
        }
    } finally {
        btn.disabled = false;
    }
}

// 4. حذف الدرس من فرع واحد فقط
function deleteSingleBranch(branchId, stageName) {
    if (!window.Swal) {
        if (!confirm(`هل تريد إزالة هذا الدرس من (${stageName}) فقط؟`)) return;
    }

    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const deleteUrl = (isUserAdmin ? '/admin/educational-contents/' : '/teacher/educational-contents/') + branchId + '?scope=single';

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
            fetch(deleteUrl, {
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

                if (activeLessonData && activeLessonData.branches) {
                    activeLessonData.branches = activeLessonData.branches.filter(b => b.id != branchId);
                    activeLessonData.branches_count = activeLessonData.branches.length;
                    
                    const badge = document.getElementById('dist_modal_branches_badge');
                    if (badge) badge.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${activeLessonData.branches_count} {{ __("فروع أكاديمية") }}`;

                    const rowCount = document.getElementById(`row_branch_count_${activeLessonData.id}`);
                    if (rowCount) rowCount.textContent = activeLessonData.branches_count;

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

// 5. حذف الدرس بالكامل من كافة الفروع
function deleteUnifiedContent(id, title, scope = 'all') {
    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const deleteUrl = (isUserAdmin ? '/admin/educational-contents/' : '/teacher/educational-contents/') + id + '?scope=all';

    Swal.fire({
        title: '{{ __("حذف المحاضرة بالكامل؟") }}',
        text: `{{ __("هل أنت متأكد من حذف") }} "${title}" {{ __("ونهائياً من كافة الفروع الأكاديمية الشقيقة وقاعدة البيانات؟") }}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("نعم، احذف المحاضرة بالكامل") }}',
        cancelButtonText: '{{ __("إلغاء") }}'
    }).then(result => {
        if (result.isConfirmed) {
            const row = document.getElementById('lesson_row_' + id);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0.3';
            }
            closeLessonModal();

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'DELETE',
                    'Accept': 'application/json'
                }
            }).then(res => res.json().catch(() => ({})))
            .then(data => {
                if (row) row.remove();
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("تم الحذف بنجاح") }}',
                    text: data.message || '{{ __("تم حذف المحاضرة من كافة الفروع بنجاح.") }}',
                    timer: 1800,
                    showConfirmButton: false
                });
            }).catch(err => {
                if (row) row.style.opacity = '1';
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر الحذف.' });
            });
        }
    });
}

// دالة ذكية وشاملة لاستخراج معرف يوتيوب YouTube Video ID من أي صيغة رابط
function extractYouTubeId(str) {
    if (!str) return null;
    const s = String(str).trim();
    if (!s || ['undefined', 'null', 'none', 'false', '0', '#'].includes(s.toLowerCase())) return null;
    if (/^[a-zA-Z0-9_\-]{11}$/.test(s)) return s;
    const m = s.match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:watch\?.*v=|embed\/|shorts\/|live\/|v\/))([a-zA-Z0-9_\-]{11})/i);
    if (m && m[1]) return m[1];
    const m2 = s.match(/[?&]v=([a-zA-Z0-9_\-]{11})/i);
    if (m2 && m2[1]) return m2[1];
    return null;
}

// 6. معاينة الفيديو المنبثقة الذكية (تدعم YouTube و Vimeo و MP4 وبث السيرفر)
function openVideoPreview(title, url, ytUrl = null, pdfUrl = null) {
    if (!url && !ytUrl) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("تنبيه") }}',
                text: '{{ __("لا يوجد رابط فيديو مسجل لهذه المحاضرة.") }}'
            });
        } else {
            alert('{{ __("لا يوجد رابط فيديو مسجل لهذه المحاضرة.") }}');
        }
        return;
    }

    const modal = document.getElementById('videoPreviewModal');
    const player = document.getElementById('preview_video_player');
    const source = document.getElementById('preview_video_source');
    const iframe = document.getElementById('preview_video_iframe');
    const titleEl = document.getElementById('preview_video_title');
    const extLink = document.getElementById('preview_video_external_link');
    const errorBox = document.getElementById('preview_video_error');
    const errorLink = document.getElementById('preview_video_error_link');
    const errorPdf = document.getElementById('preview_video_error_pdf');
    const errorH4 = document.getElementById('preview_error_h4');
    const errorP = document.getElementById('preview_error_p');

    if (titleEl) titleEl.textContent = title || '{{ __("معاينة المحاضرة") }}';

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
    if (errorH4) {
        errorH4.textContent = '{{ __("تعذر تشغيل الفيديو داخل هذا المشغل المباشر") }}';
    }
    if (errorP) {
        errorP.textContent = '{{ __("قد يكون مصدر الفيديو محمي أو غير متوافق مع العرض المضمن. بإمكانك فتح الرابط مباشرة في نافذة جديدة.") }}';
    }

    const trimmedUrl = String(url || ytUrl || '').trim();
    const isCorrupted = !trimmedUrl || 
        ['undefined', 'null', 'none', 'false', '0', '#'].includes(trimmedUrl.toLowerCase()) ||
        trimmedUrl.endsWith('/undefined') || 
        trimmedUrl.endsWith('/null');

    if (isCorrupted) {
        if (player) {
            player.pause();
            player.removeAttribute('src');
            if (source) source.removeAttribute('src');
            player.style.display = 'none';
            player.onerror = null;
        }
        if (iframe) {
            iframe.src = 'about:blank';
            iframe.style.display = 'none';
        }
        if (extLink) extLink.style.display = 'none';
        if (errorBox) errorBox.style.display = 'flex';
        if (errorH4) errorH4.textContent = 'لم يتم إرفاق ملف فيديو لهذا الدرس بعد ⚠️';
        if (errorP) errorP.textContent = 'هذا الدرس مسجل حالياً بدون ملف فيديو مرفوع أو رابط يوتيوب صالح. بإمكانك الضغط على زر تعديل الدرس لإرفاق الفيديو أو وضع رابط يوتيوب.';
        if (errorLink) errorLink.style.display = 'none';
        if (modal) modal.style.display = 'grid';
        document.body.style.overflow = 'hidden';
        return;
    }

    // 1. فحص هل المصدر فيديو YouTube من خلال الرابط الأساسي أو الاحتياطي
    const ytId = extractYouTubeId(trimmedUrl) || extractYouTubeId(url) || extractYouTubeId(ytUrl);

    if (ytId) {
        // إيقاف وتفريغ مشغل الفيديو المحلي MP4
        if (player) {
            player.pause();
            player.removeAttribute('src');
            if (source) source.removeAttribute('src');
            player.style.display = 'none';
            player.onerror = null;
        }

        const ytWatchUrl = 'https://www.youtube.com/watch?v=' + ytId;
        const ytEmbedUrl = 'https://www.youtube-nocookie.com/embed/' + ytId + '?autoplay=1&controls=1&rel=0&modestbranding=1&playsinline=1&enablejsapi=1';

        if (extLink) {
            extLink.href = ytWatchUrl;
            extLink.innerHTML = '<i class="fa-brands fa-youtube" style="color: #ef4444; font-size: 0.95rem;"></i> <span>{{ __("فتح في YouTube") }}</span>';
            extLink.title = '{{ __("مشاهدة الفيديو مباشرة على موقع YouTube") }}';
            extLink.style.display = 'inline-flex';
        }
        if (errorLink) {
            errorLink.href = ytWatchUrl;
        }

        if (iframe) {
            iframe.src = ytEmbedUrl;
            iframe.style.display = 'block';
        }
    } else if (/vimeo\.com/i.test(trimmedUrl)) {
        // 2. فحص مشغل Vimeo
        if (player) {
            player.pause();
            player.removeAttribute('src');
            if (source) source.removeAttribute('src');
            player.style.display = 'none';
            player.onerror = null;
        }

        const vm = trimmedUrl.match(/vimeo\.com\/(?:video\/)?([0-9]+)/i);
        const vimeoId = vm && vm[1] ? vm[1] : null;
        const vimeoEmbed = vimeoId ? ('https://player.vimeo.com/video/' + vimeoId + '?autoplay=1') : trimmedUrl;

        if (extLink) {
            extLink.href = trimmedUrl;
            extLink.innerHTML = '<i class="fa-brands fa-vimeo" style="color: #06b6d4; font-size: 0.95rem;"></i> <span>{{ __("فتح في Vimeo") }}</span>';
            extLink.style.display = 'inline-flex';
        }
        if (errorLink) {
            errorLink.href = trimmedUrl;
        }

        if (iframe) {
            iframe.src = vimeoEmbed;
            iframe.style.display = 'block';
        }
    } else {
        // 3. مشغل فيديو HTML5 مباشر لملفات السيرفر (MP4 / WebM وبث السيرفر)
        if (iframe) {
            iframe.src = 'about:blank';
            iframe.style.display = 'none';
        }

        if (extLink) {
            extLink.href = trimmedUrl;
            extLink.innerHTML = '<i class="fa-solid fa-arrow-up-right-from-square"></i> <span>{{ __("فتح في تبويب خارجي") }}</span>';
            extLink.style.display = 'inline-flex';
        }
        if (errorLink) {
            errorLink.href = trimmedUrl;
        }

        if (player) {
            player.style.display = 'block';
            player.pause();

            player.src = trimmedUrl;
            if (source) {
                source.src = trimmedUrl;
            }

            // نظام الاسترداد التلقائي في حال تعثر رابط البث المحلي
            player.onerror = function() {
                console.warn('Preview video load error on:', trimmedUrl);
                const backupYtId = extractYouTubeId(ytUrl);
                if (backupYtId) {
                    openVideoPreview(title, 'https://www.youtube.com/watch?v=' + backupYtId, null, pdfUrl);
                    return;
                }

                if (errorBox) {
                    errorBox.style.display = 'flex';
                }

                fetch(trimmedUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
                    .then(res => {
                        if (res.status === 422) {
                            return res.json().then(data => {
                                if (errorH4) errorH4.textContent = 'ملف هذا الدرس غير مكتمل في التخزين (حجمه 0 بايت)';
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

    if (modal) {
        modal.style.display = 'grid';
    }
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
        player.onerror = null;
    }
    if (iframe) {
        iframe.src = 'about:blank';
        iframe.style.display = 'none';
    }
    if (errorBox) {
        errorBox.style.display = 'none';
    }
    if (modal) {
        modal.style.display = 'none';
    }
    document.body.style.overflow = '';
}

document.getElementById('videoPreviewModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeVideoPreview();
});

// 7. تصفير كافة المحتويات دفعة واحدة
window.purgeAllContents = async function() {
    const isUserAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    const purgeUrl = isUserAdmin 
        ? '/admin/educational-contents/purge-all' 
        : '/teacher/educational-contents/purge-all';
    const token = '{{ csrf_token() }}';

    Swal.fire({
        title: '{{ __("هل أنت متأكد من التصفير الشامل؟") }}',
        text: '{{ __("سيتم حذف ومسح كافة الفيديوهات والشروحات نهائياً من قاعدة البيانات والسيرفر!") }}',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '{{ __("نعم، تصفير وحذف الكل") }}',
        cancelButtonText: '{{ __("إلغاء") }}'
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري الحذف والتصفير...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'DELETE');

                const res = await axios.post(purgeUrl, formData, {
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });

                if (res.data && (res.data.success || res.status === 200)) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'تم التصفير بنجاح! 🗑️',
                        text: res.data.message || 'تم حذف وتصفير جميع الفيديوهات.',
                        confirmButtonText: 'حسناً'
                    });
                    location.reload();
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: 'تعذر إتمام عملية التصفير.' });
            }
        }
    });
};

// 8. منطق رفع الفيديوهات وتوزيعها بالأجزاء (Upload Modal Scripts)
const allModalSubjects = @json($subjects ?? []);
const isModalAdmin = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};

function selectAllModalBranches(prefix, checked) {
    document.querySelectorAll('.' + prefix + '-modal-stage-check').forEach(cb => {
        cb.checked = checked;
    });
    if (prefix === 'video') onVideoModalSelectionChange();
}

function onVideoModalSelectionChange() {
    if (!isModalAdmin) return;
    const sel = document.getElementById('video_admin_subject_select');
    if (!sel) return;
    const selectedOpt = sel.options[sel.selectedIndex];
    const hiddenWrap = document.getElementById('videoHiddenSubjectIdsWrap');
    const alertBox = document.getElementById('videoPublishTargetAlert');
    const listDiv = document.getElementById('videoSelectedSubjectsList');

    if (!hiddenWrap || !alertBox || !listDiv) return;
    hiddenWrap.innerHTML = '';
    listDiv.innerHTML = '';

    if (!selectedOpt || !selectedOpt.value) {
        alertBox.style.display = 'none';
        return;
    }

    const checkedStages = Array.from(document.querySelectorAll('.video-modal-stage-check:checked')).map(cb => parseInt(cb.value));
    const cleanName = (selectedOpt.getAttribute('data-clean-name') || '').trim();
    const primaryId = parseInt(selectedOpt.value);

    const matched = allModalSubjects.filter(sub => {
        if (!checkedStages.includes(parseInt(sub.stage_id))) return false;
        if (sub.id === primaryId) return true;
        if (cleanName && sub.name_ar && sub.name_ar.includes(cleanName)) return true;
        return false;
    });

    if (matched.length === 0) {
        const pSub = allModalSubjects.find(s => s.id === primaryId);
        if (pSub) matched.push(pSub);
    }

    matched.forEach(sub => {
        hiddenWrap.innerHTML += `<input type="hidden" name="subject_ids[]" value="${sub.id}">`;
        const branchLabel = (sub.stage && sub.stage.short_label) ? sub.stage.short_label : (sub.stage ? sub.stage.label_ar : 'الفرع الأكاديمي');
        const subjectClean = (sub.clean_name || sub.name_ar || '').replace(/\(.*?\)/g, '').trim();

        listDiv.innerHTML += `
            <div style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1.5px solid #86efac; border-radius: 8px; padding: 5px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <span style="background: #15803d; color: #ffffff; font-size: 0.74rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;">${branchLabel}</span>
                <span style="color: #1e293b; font-weight: 700; font-size: 0.86rem;">${subjectClean}</span>
            </div>
        `;
    });

    hiddenWrap.innerHTML += `<input type="hidden" name="subject_id" value="${matched[0].id}">`;
    alertBox.style.display = 'block';
}

window.minimizeModalAndBrowse = function() {
    window.closeUploadVideoModal();
    if (window.EdBackgroundUploader) {
        window.EdBackgroundUploader.showWidget();
    }
    if (window.Swal) {
        Swal.fire({
            toast: true,
            position: 'bottom-start',
            icon: 'info',
            title: '🚀 الرفع مستمر بالخلفية',
            text: 'يمكنك الآن الانتقال بين الواجهات وتصفح المنصة بحرية تامة دون توقف التحميل.',
            showConfirmButton: false,
            timer: 4500
        });
    }
};

window.saveTeacherVideoDraft = function() {
    try {
        const form = document.getElementById('uploadVideoForm');
        if (!form) return;
        const data = {
            title: form.querySelector('[name="title"]')?.value || '',
            target_region: form.querySelector('[name="target_region"]:checked')?.value || 'all',
            channel_name: form.querySelector('[name="channel_name"]')?.value || '',
            order: form.querySelector('[name="order"]')?.value || '1',
            subject_id: form.querySelector('[name="subject_id"]')?.value || '',
            stage_ids: Array.from(form.querySelectorAll('[name="stage_ids[]"]:checked')).map(cb => cb.value)
        };
        localStorage.setItem('ed_teacher_video_form_draft', JSON.stringify(data));
    } catch (e) {}
};

window.restoreTeacherVideoDraft = function() {
    try {
        const raw = localStorage.getItem('ed_teacher_video_form_draft');
        if (!raw) return;
        const data = JSON.parse(raw);
        const form = document.getElementById('uploadVideoForm');
        if (!form) return;

        if (data.title && form.querySelector('[name="title"]') && !form.querySelector('[name="title"]').value) {
            form.querySelector('[name="title"]').value = data.title;
        }
        if (data.channel_name && form.querySelector('[name="channel_name"]') && !form.querySelector('[name="channel_name"]').value) {
            form.querySelector('[name="channel_name"]').value = data.channel_name;
        }
        if (data.order && form.querySelector('[name="order"]')) {
            form.querySelector('[name="order"]').value = data.order;
        }
        if (data.subject_id && form.querySelector('[name="subject_id"]') && !form.querySelector('[name="subject_id"]').value) {
            form.querySelector('[name="subject_id"]').value = data.subject_id;
        }
        if (data.target_region) {
            const rad = form.querySelector(`[name="target_region"][value="${data.target_region}"]`);
            if (rad) {
                rad.checked = true;
                updateVideoRegionSelect(rad);
            }
        }
        if (Array.isArray(data.stage_ids) && data.stage_ids.length > 0) {
            form.querySelectorAll('[name="stage_ids[]"]').forEach(cb => {
                cb.checked = data.stage_ids.includes(cb.value);
            });
        }
        try { onVideoModalSelectionChange(); } catch (e) {}
    } catch (e) {}
};

window.clearTeacherVideoDraft = function() {
    try {
        localStorage.removeItem('ed_teacher_video_form_draft');
    } catch (e) {}
};

window.openUploadVideoModal = function() {
    const m = document.getElementById('uploadVideoModal');
    if (m) {
        m.style.display = 'grid';
        document.body.style.overflow = 'hidden';
        try { window.restoreTeacherVideoDraft(); } catch (e) {}
        try { onVideoModalSelectionChange(); } catch (e) {}
        if (window.EdBackgroundUploader) {
            window.EdBackgroundUploader.syncTeacherVideosPage();
        }
    }
};

window.closeUploadVideoModal = function() {
    const m = document.getElementById('uploadVideoModal');
    if (m) {
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
};

function updateVideoRegionSelect(radio) {
    ['v_card_gaza', 'v_card_west_bank', 'v_card_all'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.borderColor = '#e2e8f0';
            el.style.background = '#ffffff';
        }
    });
    if (radio.value === 'gaza') {
        const c = document.getElementById('v_card_gaza');
        if (c) { c.style.borderColor = '#059669'; c.style.background = '#ecfdf5'; }
    } else if (radio.value === 'west_bank') {
        const c = document.getElementById('v_card_west_bank');
        if (c) { c.style.borderColor = '#1e40af'; c.style.background = '#eff6ff'; }
    } else {
        const c = document.getElementById('v_card_all');
        if (c) { c.style.borderColor = '#2563eb'; c.style.background = '#eff6ff'; }
    }
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function handleVideoFileSelection(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];
    const allowed = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
    const ext = file.name.split('.').pop().toLowerCase();
    
    if (!allowed.includes(ext)) {
        Swal.fire({
            icon: 'error',
            title: '{{ __("صيغة غير مدعومة") }}',
            text: '{{ __("يرجى اختيار ملف فيديو بصيغة MP4 أو WebM أو MOV.") }}',
            confirmButtonText: '{{ __("حسناً") }}'
        });
        input.value = '';
        return;
    }

    const titleEl = document.getElementById('dropzoneTitle');
    const subEl = document.getElementById('dropzoneSubtitle');
    const iconEl = document.querySelector('#videoDropzone .dropzone-icon');

    if (titleEl) {
        titleEl.textContent = file.name;
        titleEl.style.color = '#15803d';
    }
    if (subEl) {
        subEl.textContent = `✅ {{ __("الملف جاهز للرفع:") }} ${formatBytes(file.size)} (${ext.toUpperCase()})`;
        subEl.style.color = '#166534';
        subEl.style.fontWeight = '700';
    }
    if (iconEl) {
        iconEl.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #16a34a;"></i>';
    }
}

async function submitVideoForm(e) {
    if (e) e.preventDefault();
    const btn = document.getElementById('btnSubmitVideo');
    const btnCancel = document.getElementById('btnCancelUpload');
    const form = document.getElementById('uploadVideoForm');
    const fileInput = document.getElementById('videoFileInput');

    // حفظ مسودة الحقول
    try { window.saveTeacherVideoDraft(); } catch (err) {}

    // 1. إذا كان الفيديو مرفوعاً ومكتملاً مسبقاً في الخلفية
    let uploadedPath = null;
    let formattedSize = null;

    if (window.EdBackgroundUploader && window.EdBackgroundUploader.hasCompletedUpload()) {
        uploadedPath = window.EdBackgroundUploader.state.result.uploaded_video_path;
        formattedSize = window.EdBackgroundUploader.state.result.formatted_size;
    }

    const ytUrlInput = document.getElementById('modalYtUrlInput');
    const hasYtUrl = ytUrlInput && ytUrlInput.value.trim().length > 0;
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

    // 2. إذا لم يكن مرفوعاً مسبقاً، ولم يدخل رابط يوتيوب، ولم يختر ملف
    if (!uploadedPath && !hasYtUrl && !hasFile) {
        Swal.fire({
            icon: 'warning',
            title: '{{ __("مطلوب إرفاق الفيديو") }}',
            text: '{{ __("يرجى إدخال رابط فيديو YouTube غير مدرج أو اختيار ملف فيديو من جهازك.") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#1e3a8a'
        });
        return;
    }

    // 3. إذا كان المستخدم أدخل رابط يوتيوب ولا يريد رفع ملف محلي
    if (hasYtUrl && !hasFile && !uploadedPath) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري ربط وحفظ الدرس في الفروع...") }}';
        }
        
        try {
            const storeUrl = "{{ (auth()->check() && auth()->user()->role === 'admin') ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";
            const contentFormData = new FormData(form);
            let cleanedYt = ytUrlInput.value.trim();
            if (cleanedYt && !cleanedYt.startsWith('http://') && !cleanedYt.startsWith('https://')) {
                cleanedYt = 'https://' + cleanedYt;
            }
            contentFormData.append('video_url', cleanedYt);

            const saveRes = await axios.post(storeUrl, contentFormData, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'multipart/form-data'
                }
            });

            window.clearTeacherVideoDraft();
            
            Swal.fire({
                icon: 'success',
                title: '{{ __("تم نشر الدرس بنجاح! 🚀") }}',
                text: saveRes.data.message || '{{ __("تمت إضافة وتفعيل الشرح المرئي بنجاح.") }}',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
            return;
        } catch (err) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> <span>{{ __("بدء رفع ونشر الفيديو") }}</span>';
            }
            const msg = (err.response && err.response.data && (err.response.data.title || err.response.data.message)) 
                ? (err.response.data.title || err.response.data.message)
                : '{{ __("حدث خطأ أثناء حفظ بيانات الدرس، يرجى إعادة المحاولة.") }}';
            Swal.fire({
                icon: 'error',
                title: '{{ __("فشل حفظ الدرس") }}',
                text: msg,
                confirmButtonText: '{{ __("حسناً") }}',
                confirmButtonColor: '#dc2626'
            });
            return;
        }
    }

    const progressWrap = document.getElementById('chunkProgressWrap');
    if (progressWrap) progressWrap.style.display = 'block';
    if (btn) btn.disabled = true;

    // تمكين زر التصغير والمتابعة بالخلفية بدلاً من تعطيله
    if (btnCancel) {
        btnCancel.disabled = false;
        btnCancel.innerHTML = `<i class="fa-solid fa-window-minimize"></i> {{ __("تصغير وتصفح المنصة بالخلفية") }}`;
        btnCancel.onclick = function() { window.minimizeModalAndBrowse(); };
    }

    try {
        if (!uploadedPath && fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري فحص وتجهيز الرفع بالخلفية...") }}';

            if (window.EdBackgroundUploader) {
                const uploadPromise = window.EdBackgroundUploader.start({
                    file: file,
                    portal: '{{ auth()->user()->role ?? "teacher" }}',
                    chunkUrl: "{{ Route::has('educational_contents.upload_chunk') ? route('educational_contents.upload_chunk') : url('/educational-contents/upload-chunk') }}",
                    checkStatusUrl: "{{ Route::has('educational_contents.check_chunk_status') ? route('educational_contents.check_chunk_status') : url('/educational-contents/check-chunk-status') }}",
                    originUrl: window.location.href,
                    chunkSize: 3 * 1024 * 1024
                });

                await uploadPromise;

                if (window.EdBackgroundUploader.hasCompletedUpload()) {
                    uploadedPath = window.EdBackgroundUploader.state.result.uploaded_video_path;
                    formattedSize = window.EdBackgroundUploader.state.result.formatted_size;
                } else {
                    throw new Error(window.EdBackgroundUploader.state.error || '{{ __("تعثر استكمال رفع الفيديو.") }}');
                }
            } else {
                const uploader = new ResumableUploader({
                    chunkUrl: "{{ Route::has('educational_contents.upload_chunk') ? route('educational_contents.upload_chunk') : url('/educational-contents/upload-chunk') }}",
                    checkStatusUrl: "{{ Route::has('educational_contents.check_chunk_status') ? route('educational_contents.check_chunk_status') : url('/educational-contents/check-chunk-status') }}",
                    pingUrl: "{{ route('system.ping') }}",
                    csrfToken: '{{ csrf_token() }}'
                });
                const uploadRes = await uploader.upload(file);
                uploadedPath = uploadRes.uploaded_video_path;
                formattedSize = uploadRes.formatted_size;
            }
        }

        if (!uploadedPath || uploadedPath === 'undefined' || uploadedPath === 'null') {
            throw new Error('{{ __("لم يتم استلام مسار الفيديو النهائي من الخادم.") }}');
        }

        const progressStatus = document.getElementById('chunkProgressStatus');
        if (progressStatus) {
            progressStatus.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> {{ __("اكتمل الرفع بنجاح! جاري حفظ ونشر بيانات الدرس...") }}';
        }

        const storeUrl = "{{ (auth()->check() && auth()->user()->role === 'admin') ? route('admin.educational_contents.store') : route('teacher.educational_contents.store') }}";
        const contentFormData = new FormData(form);
        contentFormData.delete('video_file');
        if (uploadedPath && uploadedPath !== 'undefined' && uploadedPath !== 'null') {
            contentFormData.append('uploaded_video_path', uploadedPath);
        }
        if (formattedSize) {
            contentFormData.append('formatted_size', formattedSize);
        }

        const saveRes = await axios.post(storeUrl, contentFormData, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'multipart/form-data'
            }
        });

        // مسح المسودة وإعادة تعيين الودجت
        window.clearTeacherVideoDraft();
        if (window.EdBackgroundUploader) {
            window.EdBackgroundUploader.reset();
        }

        Swal.fire({
            icon: 'success',
            title: saveRes.data.title || '{{ __("تم رفع ونشر درس الفيديو بنجاح 🎉") }}',
            text: '{{ __("أصبح الفيديو متاحاً ومنظماً ضمن سجل المحاضرات الموحد!") }}',
            confirmButtonText: '{{ __("حسناً") }}',
            confirmButtonColor: '#1e3a8a'
        }).then(() => location.reload());

    } catch (err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> <span>{{ __("إعادة محاولة حفظ الفيديو") }}</span>';
        }
        if (btnCancel) {
            btnCancel.disabled = false;
            btnCancel.innerHTML = `{{ __("إلغاء") }}`;
            btnCancel.onclick = function() { window.closeUploadVideoModal(); };
        }

        let msg = err.response?.data?.message || err.message || '{{ __("حدث خطأ أثناء رفع ملف الفيديو أو حفظ الدرس") }}';
        const progressStatus = document.getElementById('chunkProgressStatus');
        if (progressStatus) {
            progressStatus.innerHTML = `<i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i> ${msg}`;
        }
        Swal.fire({
            icon: 'error',
            title: '{{ __("خطأ في رفع الفيديو") }}',
            text: msg,
            confirmButtonText: '{{ __("حسناً") }}'
        });
    }
}
window.submitVideoForm = submitVideoForm;

// استعادة المسودة تلقائياً إن وجدت عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    window.restoreTeacherVideoDraft();
});
</script>
@endsection
