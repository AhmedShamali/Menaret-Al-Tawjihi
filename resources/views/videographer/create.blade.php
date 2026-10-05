@extends('layouts.app')

@section('title', __('استوديو رفع وتوزيع المحاضرات') . ' | ' . __(config('app.name', 'Step by Step')))

@section('content')
<div class="videographer-studio-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 10px 0 50px 0;">

    {{-- 1. رأس الصفحة والمسار --}}
    <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 22px 28px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 0.82rem; color: var(--ed-text-muted); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('videographer.dashboard') }}" style="color: #1d4ed8; text-decoration: none;">{{ __('لوحة المصور') }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 0.7rem;"></i>
                <span>{{ __('استوديو الرفع والتوزيع الأكاديمي') }}</span>
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 700; color: var(--ed-text-main); margin: 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-layer-group" style="color: #1d4ed8;"></i>
                {{ __('رفع وتوزيع محاضرة مصورة على الفروع والمواد') }}
            </h1>
        </div>

        <a href="{{ route('videographer.contents.index') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; text-decoration: none; transition: background 0.15s;">
            <i class="fa-solid fa-film"></i>
            <span>{{ __('سجل المحاضرات المرفوعة') }}</span>
        </a>
    </div>

    {{-- تنبيهات الأخطاء إن وجدت --}}
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; margin-bottom: 8px;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #dc2626;"></i>
                <span>{{ __('يرجى تصحيح الأخطاء التالية قبل المتابعة:') }}</span>
            </div>
            <ul style="margin: 0; padding-right: 24px; font-size: 0.86rem; line-height: 1.6;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- تنبيه استعادة مسودة المحاضرة تلقائياً --}}
    <div id="draftRestoredAlert" style="display: none; background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af; border-radius: 12px; padding: 12px 18px; margin-bottom: 20px; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 0.88rem;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: #2563eb; font-size: 1.1rem;"></i>
            <span>{{ __('تم استعادة مسودة المحاضرة وبيانات الفروع تلقائياً لمواصلة النشر.') }}</span>
        </div>
        <button type="button" onclick="clearVideographerDraft()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #dc2626; padding: 6px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
            <i class="fa-solid fa-trash-can"></i> {{ __('مسح المسودة وبدء جديدة') }}
        </button>
    </div>

    <form id="videographerUploadForm" action="{{ route('videographer.contents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- مسار الفيديو في حال تم رفعه مسبقاً عبر Chunking --}}
        <input type="hidden" name="uploaded_video_path" id="uploadedVideoPath" value="{{ old('uploaded_video_path') }}">
        <input type="hidden" name="formatted_size" id="formattedSize" value="{{ old('formatted_size') }}">

        {{-- القسم الأول: تحديد الفروع والمواد المستهدفة (التوزيع التلقائي) --}}
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 26px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm);">
            
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 20px;">
                <div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; font-size: 0.85rem;">1</span>
                        {{ __('الفروع والمواد الأكاديمية المستهدفة (توزيع متعدد)') }}
                    </h2>
                    <p style="font-size: 0.83rem; color: var(--ed-text-muted); margin: 0;">
                        {{ __('اختر الفروع التي تشملها هذه المحاضرة؛ سيتم إسناد ونشر الفيديو تلقائياً لجميع المعلمين والطلاب في هذه الفروع معاً دون تكرار الرفع.') }}
                    </p>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="button" onclick="selectAllStages(true)" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 0.78rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> {{ __('تحديد كافة الفروع') }}
                    </button>
                    <button type="button" onclick="selectAllStages(false)" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; font-size: 0.78rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; cursor: pointer;">
                        {{ __('إلغاء التحديد') }}
                    </button>
                </div>
            </div>

            {{-- 1. شبكة اختيار الفروع (Checkboxes Cards) --}}
            <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 10px;">
                {{ __('أ) حدد الفرع أو الفروع الأكاديمية (تشيك بوكس):') }} <span style="color: #dc2626;">*</span>
            </label>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 12px; margin-bottom: 22px;">
                @foreach($stages as $stage)
                    @php
                        $stageIcon = 'fa-graduation-cap';
                        if (str_contains($stage->name, 'علمي')) $stageIcon = 'fa-flask';
                        elseif (str_contains($stage->name, 'أدبي')) $stageIcon = 'fa-book-open';
                        elseif (str_contains($stage->name, 'صناعي')) $stageIcon = 'fa-gears';
                        elseif (str_contains($stage->name, 'ريادة') || str_contains($stage->name, 'تجاري')) $stageIcon = 'fa-briefcase';
                        elseif (str_contains($stage->name, 'شرعي')) $stageIcon = 'fa-scale-balanced';
                        elseif (str_contains($stage->name, 'زراعي')) $stageIcon = 'fa-seedling';
                        elseif (str_contains($stage->name, 'فندقي')) $stageIcon = 'fa-utensils';

                        $isOldChecked = is_array(old('stage_ids')) && in_array($stage->id, old('stage_ids'));
                    @endphp
                    <label class="stage-checkbox-card" style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1.5px solid var(--ed-border); border-radius: 12px; cursor: pointer; transition: all 0.15s ease; background: var(--ed-surface); user-select: none;">
                        <input type="checkbox" name="stage_ids[]" value="{{ $stage->id }}" class="stage-checkbox" onchange="handleStageChange()" {{ $isOldChecked ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #1d4ed8; cursor: pointer;">
                        <div style="width: 34px; height: 34px; border-radius: 8px; background: #f1f5f9; color: #334155; display: grid; place-items: center; font-size: 0.95rem; flex-shrink: 0;" class="stage-icon-wrap">
                            <i class="fa-solid {{ $stageIcon }}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <strong style="display: block; font-size: 0.88rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $stage->name }}
                            </strong>
                            <span style="font-size: 0.74rem; color: #64748b;">
                                {{ $stage->subjects->count() }} {{ __('مواد مسجلة') }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- 2. اختيار المادة المشتركة أو تخصيص المواد --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 18px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                    <label style="font-size: 0.88rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-wand-magic-sparkles" style="color: #1d4ed8;"></i>
                        {{ __('ب) اختيار المادة المشتركة للربط التلقائي عبر الفروع المختارة:') }}
                    </label>
                    <span style="font-size: 0.76rem; color: #64748b; background: #fff; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        {{ __('مثال: باختيار "اللغة الإنجليزية" + فرعي العلمي والأدبي، سيتم النشر فوراً في كلا الفرعين') }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; align-items: center;">
                    <div>
                        @php
                            // استخراج المواد المشتركة الأكثر شيوعاً
                            $uniqueCleanNames = $allSubjects->map(function($sub) {
                                return trim(preg_replace('/\s*\(.*?\)\s*/u', '', $sub->name_ar ?? $sub->name));
                            })->filter()->unique()->values();
                        @endphp
                        <select name="common_name" id="commonNameSelect" onchange="handleCommonSubjectSelect()" class="uni-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #0f172a; background: #ffffff;">
                            <option value="">{{ __('--- اختر اسم المادة المشتركة (توزيع آلي) ---') }}</option>
                            @foreach($uniqueCleanNames as $cName)
                                <option value="{{ $cName }}" {{ old('common_name') == $cName ? 'selected' : '' }}>
                                    📖 {{ $cName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="quickSubjectTags" style="display: flex; flex-wrap: wrap; gap: 6px;">
                        @foreach(['اللغة الإنجليزية', 'اللغة العربية', 'التربية الإسلامية', 'الرياضيات', 'التاريخ', 'التكنولوجيا'] as $quickTag)
                            <button type="button" onclick="setQuickSubject('{{ $quickTag }}')" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-size: 0.76rem; font-weight: 600; padding: 5px 10px; border-radius: 6px; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#1d4ed8'; this.style.color='#1d4ed8';" onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#334155';">
                                + {{ $quickTag }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. جدول التأكيد التفصيلي للمواد المحددة في الفروع --}}
            <div id="stageSubjectsSummaryWrap" style="display: none; margin-top: 16px;">
                <div style="font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 8px;">
                    <i class="fa-solid fa-list-check" style="color: #059669; margin-left: 4px;"></i>
                    {{ __('المواد التي سيتم النشر والتوزيع فيها فعلياً:') }}
                </div>
                <div id="matchedSubjectsBadges" style="display: flex; flex-wrap: wrap; gap: 8px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                    {{-- ديناميكي بالجافاسكريبت --}}
                </div>
            </div>

        </div>

        {{-- القسم الثاني: بيانات المحاضرة والدرس --}}
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 26px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm);">
            
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 18px 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
                <span style="display: inline-grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; font-size: 0.85rem;">2</span>
                {{ __('تفاصيل وبيانات المحاضرة الأكاديمية') }}
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 18px;">
                
                {{-- عنوان المحاضرة --}}
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('عنوان المحاضرة / الدرس المصور') }} <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ __('مثال: الوحدة الأولى: قواعد الأزمنة Grammar - Tenses (شرح مبسط مع حل أسئلة وزارية)') }}" class="uni-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.92rem; font-weight: 600; color: #0f172a;">
                </div>

                {{-- ترتيب المحاضرة --}}
                <div>
                    <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('ترتيب الدرس / رقم المحاضرة') }}
                    </label>
                    <input type="number" name="order" value="{{ old('order', 1) }}" min="0" placeholder="1" class="uni-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #0f172a;">
                    <span style="font-size: 0.72rem; color: #64748b;">{{ __('يساعد على ترتيب المحاضرات تسلسلياً للطلاب والمعلمين') }}</span>
                </div>

                {{-- النطاق الجغرافي --}}
                <div>
                    <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('النطاق الجغرافي والمنهاج المستهدف') }}
                    </label>
                    <select name="target_region" class="uni-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #0f172a; background: #fff;">
                        <option value="all" {{ old('target_region') == 'all' ? 'selected' : '' }}>{{ __('كافة المناطق (المنهاج الفلسطيني الموحد)') }}</option>
                        <option value="west_bank" {{ old('target_region') == 'west_bank' ? 'selected' : '' }}>{{ __('الضفة الغربية والقدس') }}</option>
                        <option value="gaza" {{ old('target_region') == 'gaza' ? 'selected' : '' }}>{{ __('قطاع غزة') }}</option>
                    </select>
                </div>

                {{-- اسم المصور / جهة الإنتاج --}}
                <div>
                    <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        {{ __('اعتماد المصور / استوديو التصوير') }}
                    </label>
                    <input type="text" name="channel_name" value="{{ old('channel_name', auth()->user()->name ?? 'استوديو التصوير المعتمد') }}" placeholder="{{ __('اسم المصور أو وحدة التصوير') }}" class="uni-input" style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #0f172a;">
                </div>

            </div>

        </div>

        {{-- القسم الثالث: رفع الفيديو وملف الدوسية المرفق --}}
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 26px; margin-bottom: 24px; box-shadow: var(--ed-shadow-sm);">
            
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 18px 0; font-family: 'Alexandria', sans-serif; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
                <span style="display: inline-grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; font-size: 0.85rem;">3</span>
                {{ __('ملف الفيديو والمرفقات الأكاديمية (PDF)') }}
            </h2>

            {{-- تبديل طريقة توفير الفيديو: رفع مباشر / رفع مجزأ / رابط خارجي --}}
            <div style="display: flex; gap: 10px; margin-bottom: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                <button type="button" id="tabBtnUpload" onclick="switchVideoSource('upload')" style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 0.85rem; font-weight: 700; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('رفع ملف فيديو من الجهاز (محرك مجزأ يدعم الملفات الضخمة)') }}</span>
                </button>
                <button type="button" id="tabBtnUrl" onclick="switchVideoSource('url')" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-size: 0.85rem; font-weight: 600; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
                    <i class="fa-brands fa-youtube" style="color: #dc2626;"></i>
                    <span>{{ __('رابط خارجي (YouTube / Google Drive / Vimeo)') }}</span>
                </button>
            </div>

            {{-- خيار 1: منطقة رفع الفيديو مع شريط تقدم حي ومحرك رفع مجزأ --}}
            <div id="videoUploadZone" style="display: block; margin-bottom: 22px;">
                
                <div id="dropArea" style="border: 2px dashed #cbd5e1; border-radius: 14px; padding: 32px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s ease;">
                    <input type="file" id="videoFileInput" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska" style="display: none;" onchange="handleVideoFileSelect(this.files)">
                    
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; margin: 0 auto 12px auto; font-size: 1.6rem;">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    
                    <strong style="display: block; font-size: 0.95rem; color: #0f172a; margin-bottom: 4px;">
                        {{ __('اضغط هنا لاختيار ملف الفيديو أو اسحبه وأفلته هنا') }}
                    </strong>
                    <span style="font-size: 0.8rem; color: #64748b; display: block; margin-bottom: 12px;">
                        {{ __('الصيغ المدعومة: MP4, MOV, MKV, WEBM • يدعم الملفات الضخمة جداً (1GB, 4GB+) دون انقطاع') }}
                    </span>
                    <button type="button" onclick="document.getElementById('videoFileInput').click()" style="background: #1d4ed8; color: #ffffff; border: none; padding: 8px 18px; border-radius: 8px; font-size: 0.84rem; font-weight: 600; cursor: pointer;">
                        <i class="fa-regular fa-folder-open"></i> {{ __('استعراض ملفات الجهاز') }}
                    </button>
                </div>

                {{-- حاوية معلومات وتقدم الرفع المجزأ الحي --}}
                <div id="chunkUploadProgressWrap" style="display: none; background: #ffffff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 18px; margin-top: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; display: grid; place-items: center; font-size: 1.1rem;">
                                <i class="fa-solid fa-file-video"></i>
                            </div>
                            <div>
                                <strong id="uploadFileName" style="display: block; font-size: 0.88rem; color: #0f172a;">-</strong>
                                <span id="uploadFileSize" style="font-size: 0.76rem; color: #64748b;">-</span>
                            </div>
                        </div>
                        <div style="text-align: left;">
                            <span id="uploadPercentage" style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8; font-family: 'Alexandria', sans-serif;">0%</span>
                            <span id="uploadStatusText" style="display: block; font-size: 0.72rem; color: #64748b;">{{ __('جاري تهيئة الرفع...') }}</span>
                        </div>
                    </div>

                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-bottom: 6px;">
                        <div id="uploadProgressBar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #1d4ed8, #3b82f6); border-radius: 99px; transition: width 0.2s ease;"></div>
                    </div>

                    <div id="uploadCompletedBadge" style="display: none; color: #059669; font-size: 0.8rem; font-weight: 700; align-items: center; gap: 6px; margin-top: 6px;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('تم رفع ومعالجة ملف الفيديو بنجاح! جاهز للنشر والتوزيع.') }}</span>
                    </div>
                </div>

            </div>

            {{-- خيار 2: إدخال الرابط الخارجي --}}
            <div id="videoUrlZone" style="display: none; margin-bottom: 22px;">
                <label style="display: block; font-size: 0.86rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    {{ __('رابط الفيديو المباشر أو اليوتيوب') }}
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-link" style="position: absolute; right: 14px; top: 14px; color: #94a3b8;"></i>
                    <input type="url" name="video_url" id="videoUrlInput" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=... أو رابط مباشر" class="uni-input" style="width: 100%; height: 44px; padding: 0 40px 0 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #0f172a;">
                </div>
                <span style="font-size: 0.74rem; color: #64748b; margin-top: 4px; display: block;">
                    {{ __('يمكنك إدراج رابط يوتيوب أو Google Drive أو BunnyCDN أو أي رابط مباشر MP4.') }}
                </span>
            </div>

            {{-- ملف الدوسية أو الملخص المرفق (PDF) --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px;">
                <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                    <i class="fa-solid fa-file-pdf" style="color: #dc2626; margin-left: 6px;"></i>
                    {{ __('ملف الدوسية / ملخص المحاضرة المرفق (اختياري - PDF)') }}
                </label>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <input type="file" name="pdf_file" accept=".pdf,.docx,.zip" class="uni-input" style="flex: 1; min-width: 260px; height: 42px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; font-size: 0.85rem;">
                    <span style="font-size: 0.75rem; color: #64748b;">
                        {{ __('يتاح للطلاب والمعلمين تحميله وطباعته مع المحاضرة (حتى 60MB)') }}
                    </span>
                </div>
            </div>

        </div>

        {{-- 4. شريط الإرسال وتأكيد التوزيع --}}
        <div style="background: var(--ed-surface); border: 1px solid var(--ed-border); border-radius: var(--ed-radius-lg); padding: 22px 28px; box-shadow: var(--ed-shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <strong style="display: block; font-size: 0.95rem; color: #0f172a; margin-bottom: 2px;">
                    {{ __('تأكيد النشر والتوزيع الفوري للمحاضرة') }}
                </strong>
                <span id="distributionSummaryText" style="font-size: 0.82rem; color: #64748b;">
                    {{ __('حدد الفروع والمادة لتأكيد جهات النشر.') }}
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('videographer.dashboard') }}" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 0.9rem; text-decoration: none;">
                    {{ __('إلغاء') }}
                </a>
                <button type="submit" id="btnSubmitForm" style="display: inline-flex; align-items: center; gap: 10px; background: #1d4ed8; color: #ffffff; border: none; padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: background 0.15s; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ __('نشر وتوزيع المحاضرة فوراً') }}</span>
                </button>
            </div>
        </div>

    </form>

</div>

{{-- سكريبت التوزيع التفاعلي وحفظ واستعادة المسودة والرفع المجزأ --}}
<script>
    window.stageSubjectsMap = @json($stageSubjectsMap);
    window.selectedFile = null;
    window.isUploadingChunks = false;

    // تبديل الكل / إلغاء تحديد الفروع
    window.selectAllStages = function(checked) {
        document.querySelectorAll('.stage-checkbox').forEach(cb => {
            cb.checked = checked;
        });
        window.handleStageChange();
        if (typeof window.saveVideographerDraft === 'function') window.saveVideographerDraft();
    };

    // زر سريع لاختيار المادة
    window.setQuickSubject = function(subjectName) {
        const sel = document.getElementById('commonNameSelect');
        if (sel) {
            sel.value = subjectName;
            window.handleCommonSubjectSelect();
            if (typeof window.saveVideographerDraft === 'function') window.saveVideographerDraft();
        }
    };

    // تبديل مصدر الفيديو (رفع من الجهاز أم رابط)
    window.switchVideoSource = function(type) {
        const zoneUpload = document.getElementById('videoUploadZone');
        const zoneUrl = document.getElementById('videoUrlZone');
        const btnUpload = document.getElementById('tabBtnUpload');
        const btnUrl = document.getElementById('tabBtnUrl');

        if (!zoneUpload || !zoneUrl) return;

        if (type === 'upload') {
            zoneUpload.style.display = 'block';
            zoneUrl.style.display = 'none';
            if (btnUpload) {
                btnUpload.style.background = '#eff6ff';
                btnUpload.style.borderColor = '#bfdbfe';
                btnUpload.style.color = '#1d4ed8';
            }
            if (btnUrl) {
                btnUrl.style.background = '#ffffff';
                btnUrl.style.borderColor = '#cbd5e1';
                btnUrl.style.color = '#475569';
            }
        } else {
            zoneUpload.style.display = 'none';
            zoneUrl.style.display = 'block';
            if (btnUrl) {
                btnUrl.style.background = '#eff6ff';
                btnUrl.style.borderColor = '#bfdbfe';
                btnUrl.style.color = '#1d4ed8';
            }
            if (btnUpload) {
                btnUpload.style.background = '#ffffff';
                btnUpload.style.borderColor = '#cbd5e1';
                btnUpload.style.color = '#475569';
            }
        }
        if (typeof window.saveVideographerDraft === 'function') window.saveVideographerDraft();
    };

    // عند تغيير الفروع أو اختيار المادة المشتركة، نقوم بتحديث التوزيع التفاعلي
    window.handleStageChange = function() {
        document.querySelectorAll('.stage-checkbox-card').forEach(card => {
            const cb = card.querySelector('.stage-checkbox');
            if (cb && cb.checked) {
                card.style.borderColor = '#1d4ed8';
                card.style.background = '#eff6ff';
                const icon = card.querySelector('.stage-icon-wrap');
                if (icon) {
                    icon.style.background = '#1d4ed8';
                    icon.style.color = '#ffffff';
                }
            } else if (cb) {
                card.style.borderColor = 'var(--ed-border)';
                card.style.background = 'var(--ed-surface)';
                const icon = card.querySelector('.stage-icon-wrap');
                if (icon) {
                    icon.style.background = '#f1f5f9';
                    icon.style.color = '#334155';
                }
            }
        });

        window.handleCommonSubjectSelect();
        if (typeof window.saveVideographerDraft === 'function') window.saveVideographerDraft();
    };

    // تطبيع ومطابقة النصوص العربية وتجاوز اختلافات الهمزات والتاء المربوطة
    function normalizeArabicText(str) {
        if (!str) return '';
        return str
            .replace(/[إأآا]/g, 'ا')
            .replace(/[ةه]/g, 'ه')
            .replace(/[ىي]/g, 'ي')
            .replace(/[\u064B-\u065F]/g, '')
            .trim();
    }

    window.handleCommonSubjectSelect = function() {
        const commonSelect = document.getElementById('commonNameSelect');
        if (!commonSelect) return;
        const commonName = commonSelect.value.trim();
        const selectedStageCheckboxes = Array.from(document.querySelectorAll('.stage-checkbox:checked'));
        const selectedStageIds = selectedStageCheckboxes.map(cb => parseInt(cb.value));

        const wrap = document.getElementById('stageSubjectsSummaryWrap');
        const badgesContainer = document.getElementById('matchedSubjectsBadges');
        const summaryText = document.getElementById('distributionSummaryText');

        if (!wrap || !badgesContainer || !summaryText) return;

        if (selectedStageIds.length === 0) {
            wrap.style.display = 'none';
            summaryText.textContent = 'يرجى اختيار فرع أكاديمي واحد على الأقل.';
            summaryText.style.color = '#dc2626';
            return;
        }

        if (!commonName) {
            wrap.style.display = 'none';
            summaryText.textContent = `تم تحديد (${selectedStageIds.length}) فروع. يرجى اختيار اسم المادة لنشر المحاضرة فيها.`;
            summaryText.style.color = '#64748b';
            return;
        }

        // استخراج المواد المطابقة في الفروع المختارة بدقة
        badgesContainer.innerHTML = '';
        let matchedCount = 0;
        const normCommon = normalizeArabicText(commonName);

        selectedStageCheckboxes.forEach(cb => {
            const stageId = parseInt(cb.value);
            const stageCard = cb.closest('.stage-checkbox-card');
            const stageName = stageCard ? stageCard.querySelector('strong').textContent.trim() : `فرع #${stageId}`;
            const stageSubjects = (window.stageSubjectsMap && window.stageSubjectsMap[stageId]) ? window.stageSubjectsMap[stageId] : [];

            // البحث عن المادة بالاسم المنظف والمطبع أو المفتاح الأساسي
            const matched = stageSubjects.filter(sub => {
                const clean = (sub.clean_name || '').trim();
                const nameAr = (sub.name_ar || sub.name || '').trim();
                const normClean = normalizeArabicText(clean);
                const normNameAr = normalizeArabicText(nameAr);

                return (normClean && (normClean.includes(normCommon) || normCommon.includes(normClean)))
                    || (normNameAr && (normNameAr.includes(normCommon) || normCommon.includes(normNameAr)));
            });

            matched.forEach(sub => {
                matchedCount++;
                const badge = document.createElement('div');
                badge.style.display = 'inline-flex';
                badge.style.alignItems = 'center';
                badge.style.gap = '6px';
                badge.style.background = '#ecfdf5';
                badge.style.color = '#065f46';
                badge.style.border = '1px solid #a7f3d0';
                badge.style.padding = '6px 12px';
                badge.style.borderRadius = '8px';
                badge.style.fontSize = '0.8rem';
                badge.style.fontWeight = '700';

                badge.innerHTML = `
                    <i class="fa-solid fa-circle-check" style="color: #059669;"></i>
                    <span>${stageName}:</span>
                    <strong style="color: #047857;">${sub.name_ar || sub.name}</strong>
                    <input type="hidden" name="subject_ids[]" value="${sub.id}">
                `;
                badgesContainer.appendChild(badge);
            });
        });

        if (matchedCount > 0) {
            wrap.style.display = 'block';
            summaryText.innerHTML = `<strong style="color: #059669;"><i class="fa-solid fa-circle-check"></i> سيتم النشر والتوزيع فوراً على (${matchedCount}) مواد في (${selectedStageIds.length}) فروع أكاديمية دفعة واحدة.</strong>`;
        } else {
            wrap.style.display = 'block';
            badgesContainer.innerHTML = `<span style="color: #dc2626; font-size: 0.8rem; font-weight: 600;"><i class="fa-solid fa-triangle-exclamation"></i> لم يتم العثور على مادة باسم "${commonName}" في الفروع المختارة. يمكنك كتابة الاسم بدقة أو التأكد من إدراج المادة في تلك الفروع.</span>`;
            summaryText.textContent = 'لم يتم مطابقة أي مادة في الفروع المحددة.';
            summaryText.style.color = '#dc2626';
        }
    };

    // حفظ مسودة النموذج في التخزين المحلي لمنع فقدان البيانات عند الانتقال لصفحات أخرى
    window.saveVideographerDraft = function() {
        const form = document.getElementById('videographerUploadForm');
        if (!form) return;

        // منع الحفظ أثناء عملية استعادة المسودة الجارية
        if (window._isRestoringDraft) return;

        try {
            const title = form.querySelector('input[name="title"]')?.value || '';
            const commonName = form.querySelector('select[name="common_name"]')?.value || '';
            const order = form.querySelector('input[name="order"]')?.value || '';
            const targetRegion = form.querySelector('select[name="target_region"]')?.value || 'all';
            const channelName = form.querySelector('input[name="channel_name"]')?.value || '';
            const videoUrl = form.querySelector('input[name="video_url"]')?.value || '';
            const stageIds = Array.from(form.querySelectorAll('.stage-checkbox:checked')).map(cb => cb.value);
            const uploadedPath = document.getElementById('uploadedVideoPath')?.value || '';
            const formattedSize = document.getElementById('formattedSize')?.value || '';
            const fileName = document.getElementById('uploadFileName')?.textContent || '';

            // حماية: لا نحفظ نموذجاً فارغاً بالكامل لتجنب مسح مسودة سابقة دون قصد
            const hasData = title || commonName || (stageIds && stageIds.length > 0) || videoUrl || uploadedPath;
            if (!hasData) {
                return;
            }

            const draft = {
                title: title,
                common_name: commonName,
                order: order,
                target_region: targetRegion,
                channel_name: channelName,
                video_url: videoUrl,
                stage_ids: stageIds,
                uploaded_video_path: uploadedPath,
                formatted_size: formattedSize,
                file_name: fileName,
                activeTab: document.getElementById('videoUploadZone')?.style.display === 'none' ? 'url' : 'upload',
                timestamp: Date.now()
            };
            localStorage.setItem('ed_videographer_form_draft', JSON.stringify(draft));
        } catch (e) {}
    };

    // استعادة مسودة النموذج المحفوظة
    window.restoreVideographerDraft = function() {
        const form = document.getElementById('videographerUploadForm');
        if (!form) return;

        const saved = localStorage.getItem('ed_videographer_form_draft');
        if (!saved) {
            // تحقق إن كان هناك رفع نشط أو مكتمل في المحرك العام
            if (window.EdBackgroundUploader && (window.EdBackgroundUploader.hasActiveUpload() || window.EdBackgroundUploader.hasCompletedUpload())) {
                window.syncVideographerUploadUI(window.EdBackgroundUploader.state);
            }
            return;
        }

        try {
            window._isRestoringDraft = true;
            const draft = JSON.parse(saved);

            // صلاحية المسودة 24 ساعة
            if (Date.now() - draft.timestamp > 86400000) {
                localStorage.removeItem('ed_videographer_form_draft');
                window._isRestoringDraft = false;
                return;
            }

            let hasContent = false;

            if (draft.title && form.querySelector('input[name="title"]')) {
                form.querySelector('input[name="title"]').value = draft.title;
                hasContent = true;
            }
            if (draft.order && form.querySelector('input[name="order"]')) {
                form.querySelector('input[name="order"]').value = draft.order;
            }
            if (draft.target_region && form.querySelector('select[name="target_region"]')) {
                form.querySelector('select[name="target_region"]').value = draft.target_region;
            }
            if (draft.channel_name && form.querySelector('input[name="channel_name"]')) {
                form.querySelector('input[name="channel_name"]').value = draft.channel_name;
            }
            if (draft.video_url && form.querySelector('input[name="video_url"]')) {
                form.querySelector('input[name="video_url"]').value = draft.video_url;
                hasContent = true;
            }

            if (Array.isArray(draft.stage_ids) && draft.stage_ids.length > 0) {
                form.querySelectorAll('.stage-checkbox').forEach(cb => {
                    cb.checked = draft.stage_ids.includes(cb.value);
                });
                hasContent = true;
            }

            if (draft.common_name && form.querySelector('select[name="common_name"]')) {
                form.querySelector('select[name="common_name"]').value = draft.common_name;
                hasContent = true;
            }

            if (draft.activeTab) {
                window.switchVideoSource(draft.activeTab);
            }

            // تطبيق التنسيقات وتحديث قائمة المواد المطابقة
            window.handleStageChange();

            // استعادة بيانات الفيديو إذا كان هناك فيديو مكتمل محفوظ
            const uploadedPath = draft.uploaded_video_path || 
                (window.EdBackgroundUploader && window.EdBackgroundUploader.state?.result?.uploaded_video_path);

            if (uploadedPath) {
                const uploadedPathInput = document.getElementById('uploadedVideoPath');
                const formattedSizeInput = document.getElementById('formattedSize');
                const progressWrap = document.getElementById('chunkUploadProgressWrap');
                const fileNameEl = document.getElementById('uploadFileName');
                const fileSizeEl = document.getElementById('uploadFileSize');
                const percentageEl = document.getElementById('uploadPercentage');
                const progressBar = document.getElementById('uploadProgressBar');
                const completedBadge = document.getElementById('uploadCompletedBadge');
                const statusText = document.getElementById('uploadStatusText');
                const btnSubmit = document.getElementById('btnSubmitForm');

                if (uploadedPathInput) uploadedPathInput.value = uploadedPath;
                if (formattedSizeInput) formattedSizeInput.value = draft.formatted_size || window.EdBackgroundUploader?.state?.result?.formatted_size || '';
                if (progressWrap) progressWrap.style.display = 'block';
                if (fileNameEl) fileNameEl.textContent = draft.file_name || window.EdBackgroundUploader?.state?.fileName || 'فيديو تم رفعه بالخلفية';
                if (fileSizeEl) fileSizeEl.textContent = draft.formatted_size || window.EdBackgroundUploader?.state?.fileSizeFormatted || '';
                if (percentageEl) percentageEl.textContent = '100%';
                if (progressBar) progressBar.style.width = '100%';
                if (completedBadge) completedBadge.style.display = 'flex';
                if (statusText) statusText.textContent = 'تم رفع الفيديو ومعالجته بنجاح! جاهز للنشر والتوزيع.';
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.style.opacity = '1';
                    btnSubmit.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span>نشر وتوزيع المحاضرة فوراً</span>`;
                }
                hasContent = true;
            }

            // إذا كان هناك رفع جاري حالياً بالخلفية نقوم بمزامنته
            if (window.EdBackgroundUploader && window.EdBackgroundUploader.hasActiveUpload()) {
                window.syncVideographerUploadUI(window.EdBackgroundUploader.state);
            }

            // إظهار شارة الاستعادة التلقائية
            if (hasContent) {
                const draftAlert = document.getElementById('draftRestoredAlert');
                if (draftAlert) draftAlert.style.display = 'flex';
            }
        } catch (e) {
            console.warn('Draft restore error:', e);
        } finally {
            window._isRestoringDraft = false;
        }
    };

    // مسح المسودة لبدء محاضرة جديدة تماماً
    window.clearVideographerDraft = function() {
        localStorage.removeItem('ed_videographer_form_draft');
        const form = document.getElementById('videographerUploadForm');
        if (form) {
            form.reset();
            const uploadedPath = document.getElementById('uploadedVideoPath');
            if (uploadedPath) uploadedPath.value = '';
            const formattedSize = document.getElementById('formattedSize');
            if (formattedSize) formattedSize.value = '';
            window.handleStageChange();
        }
        const draftAlert = document.getElementById('draftRestoredAlert');
        if (draftAlert) draftAlert.style.display = 'none';

        const progressWrap = document.getElementById('chunkUploadProgressWrap');
        if (progressWrap && (!window.EdBackgroundUploader || !window.EdBackgroundUploader.hasActiveUpload())) {
            progressWrap.style.display = 'none';
        }
    };

    // معالجة اختيار ملف الفيديو
    window.handleVideoFileSelect = function(files) {
        if (!files || files.length === 0) return;
        window.selectedFile = files[0];

        const progressWrap = document.getElementById('chunkUploadProgressWrap');
        const fileNameEl = document.getElementById('uploadFileName');
        const fileSizeEl = document.getElementById('uploadFileSize');
        const formattedSizeHidden = document.getElementById('formattedSize');

        const mbSize = (window.selectedFile.size / 1048576).toFixed(1) + ' MB';
        if (fileNameEl) fileNameEl.textContent = window.selectedFile.name;
        if (fileSizeEl) fileSizeEl.textContent = mbSize;
        if (formattedSizeHidden) formattedSizeHidden.value = mbSize;
        if (progressWrap) progressWrap.style.display = 'block';

        // ملء عنوان المحاضرة تلقائياً من اسم الملف إذا كان حقل العنوان فارغاً
        const titleInput = document.querySelector('input[name="title"]');
        if (titleInput && !titleInput.value.trim()) {
            const rawTitle = window.selectedFile.name.replace(/\.[^/.]+$/, "");
            titleInput.value = rawTitle;
            if (typeof window.saveVideographerDraft === 'function') window.saveVideographerDraft();
        }

        // بدء الرفع بالخلفية عبر محرك المنصة العام
        if (window.EdBackgroundUploader) {
            window.EdBackgroundUploader.start({
                file: window.selectedFile,
                portal: 'videographer',
                chunkUrl: "{{ route('videographer.contents.upload_chunk') }}",
                checkStatusUrl: "{{ route('videographer.contents.check_chunk_status') }}",
                originUrl: window.location.href,
                chunkSize: 3 * 1024 * 1024
            });
        }
    };

    // مزامنة عناصر شاشة الرفع مع حالة الرفع في الخلفية
    window.syncVideographerUploadUI = function(state) {
        if (!state) return;
        const progressWrap = document.getElementById('chunkUploadProgressWrap');
        const fileNameEl = document.getElementById('uploadFileName');
        const fileSizeEl = document.getElementById('uploadFileSize');
        const percentageEl = document.getElementById('uploadPercentage');
        const progressBar = document.getElementById('uploadProgressBar');
        const statusText = document.getElementById('uploadStatusText');
        const completedBadge = document.getElementById('uploadCompletedBadge');
        const uploadedVideoPath = document.getElementById('uploadedVideoPath');
        const formattedSizeHidden = document.getElementById('formattedSize');
        const btnSubmit = document.getElementById('btnSubmitForm');

        if (!progressWrap) return;

        if (state.status === 'uploading' || state.status === 'paused') {
            window.isUploadingChunks = true;
            progressWrap.style.display = 'block';
            if (fileNameEl && state.fileName) fileNameEl.textContent = state.fileName;
            if (fileSizeEl && state.fileSizeFormatted) fileSizeEl.textContent = state.fileSizeFormatted;
            if (percentageEl) percentageEl.textContent = state.progress + '%';
            if (progressBar) progressBar.style.width = state.progress + '%';
            if (completedBadge) completedBadge.style.display = 'none';

            if (statusText) {
                if (state.status === 'paused') {
                    statusText.innerHTML = '<span style="color: #d97706;"><i class="fa-solid fa-triangle-exclamation fa-beat"></i> انقطع النت (معلّق).. جاري الاستئناف التلقائي فور العودة</span>';
                } else {
                    statusText.textContent = `جاري رفع أجزاء الفيديو في الخلفية: ${state.partText || ''} (${state.speed || ''}) متبقي: ${state.eta || ''}`;
                }
            }

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.6';
                btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري رفع أجزاء الفيديو (${state.progress}%)...</span>`;
            }
        } else if (state.status === 'completed' && state.result) {
            window.isUploadingChunks = false;
            progressWrap.style.display = 'block';
            if (fileNameEl && state.fileName) fileNameEl.textContent = state.fileName;
            if (fileSizeEl && state.fileSizeFormatted) fileSizeEl.textContent = state.fileSizeFormatted;
            if (percentageEl) percentageEl.textContent = '100%';
            if (progressBar) progressBar.style.width = '100%';
            if (completedBadge) completedBadge.style.display = 'flex';
            if (statusText) statusText.textContent = 'تم رفع ومعالجة الفيديو بنجاح! جاهز للنشر والتوزيع.';

            if (uploadedVideoPath) uploadedVideoPath.value = state.result.uploaded_video_path;
            if (formattedSizeHidden) formattedSizeHidden.value = state.result.formatted_size;

            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span>نشر وتوزيع المحاضرة فوراً</span>`;
            }
        } else if (state.status === 'error') {
            window.isUploadingChunks = false;
            if (statusText) statusText.textContent = state.error || 'حدث خطأ أثناء الرفع.';
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span>نشر وتوزيع المحاضرة فوراً</span>`;
            }
        }
    };

    // ربط مستمع الحالة مع EdBackgroundUploader
    if (window.EdBackgroundUploader) {
        window.EdBackgroundUploader.onStateChange(window.syncVideographerUploadUI);
    }

    // تجهيز السحب والإفلات
    const dropAreaEl = document.getElementById('dropArea');
    if (dropAreaEl) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropAreaEl.addEventListener(eventName, (e) => { e.preventDefault(); e.stopPropagation(); }, false);
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            dropAreaEl.addEventListener(eventName, () => {
                dropAreaEl.style.borderColor = '#1d4ed8';
                dropAreaEl.style.background = '#eff6ff';
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropAreaEl.addEventListener(eventName, () => {
                dropAreaEl.style.borderColor = '#cbd5e1';
                dropAreaEl.style.background = '#f8fafc';
            }, false);
        });
        dropAreaEl.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                window.handleVideoFileSelect(dt.files);
            }
        });
    }

    // إعداد النموذج وتثبيت الحفظ التلقائي عند الكتابة
    const vgForm = document.getElementById('videographerUploadForm');
    if (vgForm) {
        vgForm.addEventListener('input', window.saveVideographerDraft);
        vgForm.addEventListener('change', window.saveVideographerDraft);

        vgForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // 1. التحقق هل يوجد رفع فيديو جاري في الخلفية
            if (window.EdBackgroundUploader && window.EdBackgroundUploader.hasActiveUpload()) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'الرفع قيد التقدم',
                        text: 'يرجى الانتظار حتى اكتمال رفع الفيديو في الخلفية أولاً.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى الانتظار حتى اكتمال رفع الفيديو في الخلفية أولاً.');
                }
                return false;
            }

            // 2. التحقق من اختيار الفروع
            const selectedStageCheckboxes = document.querySelectorAll('.stage-checkbox:checked');
            if (selectedStageCheckboxes.length === 0) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'لم يتم تحديد أي فرع',
                        text: 'يرجى تحديد فرع أكاديمي واحد على الأقل لنشر المحاضرة فيه.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى تحديد فرع أكاديمي واحد على الأقل.');
                }
                return false;
            }

            // 3. التحقق من وجود عنوان للمحاضرة
            const titleInput = vgForm.querySelector('input[name="title"]');
            if (!titleInput || !titleInput.value.trim()) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'عنوان المحاضرة مطلوب',
                        text: 'يرجى كتابة عنوان للمحاضرة قبل المتابعة.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى كتابة عنوان للمحاضرة.');
                }
                if (titleInput) titleInput.focus();
                return false;
            }

            // 4. التحقق من مصدر الفيديو (مسار مرفوع مسبقاً أو رابط أو رفع مباشر)
            const uploadedVideoPath = document.getElementById('uploadedVideoPath');
            const videoUrlInput = document.getElementById('videoUrlInput');
            const videoFileInput = document.getElementById('videoFileInput');

            // مزامنة فورية إذا كان الرفع مكتملاً في EdBackgroundUploader ولكن الحقل المخفي فارغ
            if ((!uploadedVideoPath || !uploadedVideoPath.value) && window.EdBackgroundUploader && window.EdBackgroundUploader.hasCompletedUpload()) {
                if (uploadedVideoPath) uploadedVideoPath.value = window.EdBackgroundUploader.state.result.uploaded_video_path;
            }

            const hasUploadedPath = uploadedVideoPath && uploadedVideoPath.value.trim().length > 0;
            const hasUrl = videoUrlInput && videoUrlInput.value.trim().length > 0;
            const hasRawFile = videoFileInput && videoFileInput.files && videoFileInput.files.length > 0;

            if (!hasUploadedPath && !hasUrl && !hasRawFile) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ملف الفيديو مطلوب',
                        text: 'يرجى اختيار ملف فيديو ورفعه، أو إدراج رابط يوتيوب/خارجي للمحاضرة.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى اختيار ملف فيديو أو إدراج رابط.');
                }
                return false;
            }

            const btn = document.getElementById('btnSubmitForm');
            const originalBtnHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> <span>جاري النشر والتوزيع الأكاديمي...</span>`;
            }

            try {
                const formData = new FormData(vgForm);
                // حماية مؤكدة: إذا تم رفع الفيديو عبر أجزاء Chunks، نحذف ملف الفيديو الخام من حمولة النموذج
                // حتى لا يتم إرسال ملف ضخم (400MB+) عبر HTTP POST عادي ويتسبب بتجميد المتصفح
                if (hasUploadedPath) {
                    formData.delete('video_file');
                }

                const response = await fetch(vgForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => null);

                if (response.ok && data && data.success) {
                    // مسح مسودة النموذج بعد النجاح المؤكد
                    localStorage.removeItem('ed_videographer_form_draft');
                    sessionStorage.removeItem('ed_bg_upload_completed');
                    if (window.EdBackgroundUploader) {
                        window.EdBackgroundUploader.state.status = 'idle';
                        window.EdBackgroundUploader.state.result = null;
                        window.EdBackgroundUploader.hideWidget();
                    }

                    if (window.Swal) {
                        await Swal.fire({
                            icon: 'success',
                            title: '🎉 تم النشر والتوزيع بنجاح!',
                            text: data.message || 'تم نشر وتوزيع المحاضرة فورياً على الفروع والمواد المحددة.',
                            confirmButtonText: 'الذهاب لسجل المحاضرات',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }

                    window.location.href = data.redirect || "{{ route('videographer.contents.index') }}";
                } else {
                    // فشل التحقق أو خطأ من السيرفر: إعادة تمكين الزر فوراً
                    if (btn) {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.innerHTML = originalBtnHtml;
                    }

                    let errorMsg = 'تعذر حفظ وتوزيع المحاضرة. يرجى مراجعة البيانات.';
                    if (data && data.errors) {
                        const firstKey = Object.keys(data.errors)[0];
                        errorMsg = Array.isArray(data.errors[firstKey]) ? data.errors[firstKey][0] : data.errors[firstKey];
                    } else if (data && data.message) {
                        errorMsg = data.message;
                    }

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'تنبيه',
                            text: errorMsg,
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert(errorMsg);
                    }
                }
            } catch (err) {
                console.error("Submission error:", err);
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.innerHTML = originalBtnHtml;
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في الاتصال',
                        text: 'حدث خطأ أثناء الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت وإعادة المحاولة.',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('حدث خطأ أثناء إرسال البيانات. يرجى إعادة المحاولة.');
                }
            }
        });
    }

    // تشغيل التهيئة واستعادة المسودة فور تحميل الواجهة
    window.restoreVideographerDraft();
    window.handleStageChange();

    // تشغيل الاستعادة عند العودة من صفحة أخرى عبر الملاحة السلسة
    window.addEventListener('ed:page-loaded', function() {
        window.restoreVideographerDraft();
        window.handleStageChange();
        if (window.EdBackgroundUploader) {
            window.syncVideographerUploadUI(window.EdBackgroundUploader.state);
        }
    });

    // حفظ المسودة تلقائياً قبل إغلاق أو تحديث التبويب
    window.addEventListener('beforeunload', function() {
        if (typeof window.saveVideographerDraft === 'function') {
            window.saveVideographerDraft();
        }
    });
</script>
@endsection
