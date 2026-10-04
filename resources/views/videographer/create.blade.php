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

{{-- سكريبت التوزيع التفاعلي والرفع المجزأ --}}
<script>
    const stageSubjectsMap = @json($stageSubjectsMap);
    let selectedFile = null;
    let isUploadingChunks = false;

    // تبديل الكل / إلغاء تحديد الفروع
    function selectAllStages(checked) {
        document.querySelectorAll('.stage-checkbox').forEach(cb => {
            cb.checked = checked;
        });
        handleStageChange();
    }

    // زر سريع لاختيار المادة
    function setQuickSubject(subjectName) {
        const sel = document.getElementById('commonNameSelect');
        sel.value = subjectName;
        handleCommonSubjectSelect();
    }

    // تبديل مصدر الفيديو (رفع من الجهاز أم رابط)
    function switchVideoSource(type) {
        const zoneUpload = document.getElementById('videoUploadZone');
        const zoneUrl = document.getElementById('videoUrlZone');
        const btnUpload = document.getElementById('tabBtnUpload');
        const btnUrl = document.getElementById('tabBtnUrl');

        if (type === 'upload') {
            zoneUpload.style.display = 'block';
            zoneUrl.style.display = 'none';
            btnUpload.style.background = '#eff6ff';
            btnUpload.style.borderColor = '#bfdbfe';
            btnUpload.style.color = '#1d4ed8';
            btnUrl.style.background = '#ffffff';
            btnUrl.style.borderColor = '#cbd5e1';
            btnUrl.style.color = '#475569';
        } else {
            zoneUpload.style.display = 'none';
            zoneUrl.style.display = 'block';
            btnUrl.style.background = '#eff6ff';
            btnUrl.style.borderColor = '#bfdbfe';
            btnUrl.style.color = '#1d4ed8';
            btnUpload.style.background = '#ffffff';
            btnUpload.style.borderColor = '#cbd5e1';
            btnUpload.style.color = '#475569';
        }
    }

    // عند تغيير الفروع أو اختيار المادة المشتركة، نقوم بتحديث التوزيع التفاعلي
    function handleStageChange() {
        // تحديث مظهر كروت الفروع المحددة
        document.querySelectorAll('.stage-checkbox-card').forEach(card => {
            const cb = card.querySelector('.stage-checkbox');
            if (cb.checked) {
                card.style.borderColor = '#1d4ed8';
                card.style.background = '#eff6ff';
                card.querySelector('.stage-icon-wrap').style.background = '#1d4ed8';
                card.querySelector('.stage-icon-wrap').style.color = '#ffffff';
            } else {
                card.style.borderColor = 'var(--ed-border)';
                card.style.background = 'var(--ed-surface)';
                card.querySelector('.stage-icon-wrap').style.background = '#f1f5f9';
                card.querySelector('.stage-icon-wrap').style.color = '#334155';
            }
        });

        handleCommonSubjectSelect();
    }

    function handleCommonSubjectSelect() {
        const commonName = document.getElementById('commonNameSelect').value.trim();
        const selectedStageCheckboxes = Array.from(document.querySelectorAll('.stage-checkbox:checked'));
        const selectedStageIds = selectedStageCheckboxes.map(cb => parseInt(cb.value));

        const wrap = document.getElementById('stageSubjectsSummaryWrap');
        const badgesContainer = document.getElementById('matchedSubjectsBadges');
        const summaryText = document.getElementById('distributionSummaryText');

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

        // استخراج المواد المطابقة في الفروع المختارة
        badgesContainer.innerHTML = '';
        let matchedCount = 0;

        selectedStageCheckboxes.forEach(cb => {
            const stageId = parseInt(cb.value);
            const stageName = cb.closest('.stage-checkbox-card').querySelector('strong').textContent.trim();
            const stageSubjects = stageSubjectsMap[stageId] || [];

            // البحث عن المادة بالاسم النظيف
            const matched = stageSubjects.filter(sub => {
                return sub.clean_name.includes(commonName) || commonName.includes(sub.clean_name) || sub.name_ar.includes(commonName);
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
                badge.style.padding = '5px 10px';
                badge.style.borderRadius = '8px';
                badge.style.fontSize = '0.78rem';
                badge.style.fontWeight = '700';

                badge.innerHTML = `
                    <i class="fa-solid fa-check" style="color: #059669;"></i>
                    <span>${stageName}:</span>
                    <strong style="color: #047857;">${sub.name_ar}</strong>
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
    }

    // ==========================================
    // محرك الرفع المجزأ للفيديوهات الكبيرة (Chunked Upload Engine)
    // ==========================================
    const dropArea = document.getElementById('dropArea');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.style.borderColor = '#1d4ed8';
            dropArea.style.background = '#eff6ff';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.style.borderColor = '#cbd5e1';
            dropArea.style.background = '#f8fafc';
        }, false);
    });

    dropArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleVideoFileSelect(files);
    });

    function handleVideoFileSelect(files) {
        if (!files || files.length === 0) return;
        selectedFile = files[0];

        const progressWrap = document.getElementById('chunkUploadProgressWrap');
        const fileNameEl = document.getElementById('uploadFileName');
        const fileSizeEl = document.getElementById('uploadFileSize');
        const formattedSizeHidden = document.getElementById('formattedSize');

        const mbSize = (selectedFile.size / 1048576).toFixed(1) + ' MB';
        fileNameEl.textContent = selectedFile.name;
        fileSizeEl.textContent = mbSize;
        formattedSizeHidden.value = mbSize;
        progressWrap.style.display = 'block';

        // بدء الرفع التلقائي بالخلفية عبر محرك الرفع المستمر للمنصة
        if (window.EdBackgroundUploader) {
            window.EdBackgroundUploader.start({
                file: selectedFile,
                portal: 'videographer',
                chunkUrl: "{{ route('videographer.contents.upload_chunk') }}",
                checkStatusUrl: "{{ route('videographer.contents.check_chunk_status') }}",
                originUrl: window.location.href,
                chunkSize: 3 * 1024 * 1024
            });
        }
    }

    // مزامنة حالة الرفع بالخلفية مع عناصر الصفحة
    function syncVideographerUploadUI(state) {
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
            isUploadingChunks = true;
            progressWrap.style.display = 'block';
            if (fileNameEl) fileNameEl.textContent = state.fileName;
            if (fileSizeEl) fileSizeEl.textContent = state.fileSizeFormatted;
            if (percentageEl) percentageEl.textContent = state.progress + '%';
            if (progressBar) progressBar.style.width = state.progress + '%';
            if (completedBadge) completedBadge.style.display = 'none';

            if (statusText) {
                if (state.status === 'paused') {
                    statusText.innerHTML = '<span style="color: #d97706;"><i class="fa-solid fa-triangle-exclamation fa-beat"></i> انقطع النت (معلّق).. جاري الاستئناف التلقائي</span>';
                } else {
                    statusText.textContent = `جاري رفع أجزاء الفيديو: ${state.partText || ''} (${state.speed || ''}) متبقي: ${state.eta || ''}`;
                }
            }

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.6';
                btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>جاري رفع أجزاء الفيديو (${state.progress}%)...</span>`;
            }
        } else if (state.status === 'completed' && state.result) {
            isUploadingChunks = false;
            progressWrap.style.display = 'block';
            if (fileNameEl) fileNameEl.textContent = state.fileName;
            if (fileSizeEl) fileSizeEl.textContent = state.fileSizeFormatted;
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
            isUploadingChunks = false;
            if (statusText) statusText.textContent = state.error || 'حدث خطأ أثناء الرفع.';
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span>نشر وتوزيع المحاضرة فوراً</span>`;
            }
        }
    }

    if (window.EdBackgroundUploader) {
        window.EdBackgroundUploader.onStateChange(syncVideographerUploadUI);
    }

    // معالجة إرسال النموذج وحظر الازدواجية
    document.getElementById('videographerUploadForm').addEventListener('submit', function(e) {
        if (window.EdBackgroundUploader && window.EdBackgroundUploader.hasActiveUpload()) {
            e.preventDefault();
            alert('يرجى الانتظار حتى اكتمال رفع الفيديو في الخلفية أولاً.');
            return false;
        }

        const selectedStageCheckboxes = document.querySelectorAll('.stage-checkbox:checked');
        if (selectedStageCheckboxes.length === 0) {
            e.preventDefault();
            alert('يرجى تحديد فرع أكاديمي واحد على الأقل.');
            return false;
        }

        const uploadedVideoPath = document.getElementById('uploadedVideoPath');
        const videoFileInput = document.getElementById('videoFileInput');
        // إذا كان الفيديو مرفوعاً مسبقاً عبر Chunking نزيل اسم الحقل لكي لا يحاول المتصفح رفعه مجدداً عبر الـ POST
        if (uploadedVideoPath && uploadedVideoPath.value && videoFileInput) {
            videoFileInput.removeAttribute('name');
        }

        const btn = document.getElementById('btnSubmitForm');
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> <span>جاري النشر والتوزيع الأكاديمي...</span>`;
    });

    // تشغيل التحديد المبدئي إذا كان هناك قيم قديمة
    document.addEventListener('DOMContentLoaded', function() {
        handleStageChange();
    });
</script>
@endsection
