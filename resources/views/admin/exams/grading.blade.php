@extends('layouts.app')

@section('title', 'رصد درجات الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.6s ease;">

    <!-- هيدر الصفحة -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <nav style="font-size: 0.8rem; color: #64748b; margin-bottom: 8px; font-weight: 600;">بوابة التصحيح / سجل التسليمات / رصد الدرجات</nav>
            <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; letter-spacing: -1px; margin: 0;">مراجعة الحلول الأكاديمية 🖋️</h1>
            <p style="margin-top: 5px; color: #64748b; font-size: 0.92rem;">{{ __('رصد درجات الطالب:') }} <strong style="color: #1e40af;">{{ $submission->student->name_ar ?? $submission->student->name }}</strong></p>
        </div>
        <div style="text-align: left; background: #fff; padding: 12px 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">{{ __('الدرجة الحالية') }}</div>
            <div style="font-size: 2.2rem; font-weight: 900; color: #1e40af; font-family: monospace;">{{ $submission->total_earned_grade ?? 0 }}</div>
        </div>
    </div>

    {{-- تقرير النزاهة الأكاديمية ومراقبة الغش --}}
    @php
        $hasCheating = ($submission->has_cheating_risk || $submission->tab_switches_count > 0 || $submission->screenshots_count > 0);
    @endphp
    @if($hasCheating)
        <div style="margin-bottom: 30px; background: #fef2f2; border: 2px solid #fecaca; border-radius: 16px; padding: 20px 25px; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.08);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.5rem;">⚠️</span>
                    <div>
                        <h3 style="margin: 0; color: #991b1b; font-size: 1.1rem; font-weight: 800;">{{ __('تقرير النزاهة الأكاديمية: تم رصد أنشطة اشتباه غش') }}</h3>
                        <p style="margin: 3px 0 0; color: #b91c1c; font-size: 0.84rem;">{{ __('قام النظام بمراقبة تصرفات الطالب أثناء حل الاختبار وتسجيل محاولات الخروج والتقاط الشاشة.') }}</p>
                    </div>
                </div>
                <span style="background: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.78rem;">
                    {{ __('تنبيه المعلم نشط') }}
                </span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #fee2e2;">
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ __('مرات مغادرة نافذة الامتحان') }}</span>
                    <strong style="font-size: 1.5rem; color: #dc2626; font-family: monospace;">{{ $submission->tab_switches_count ?? 0 }}</strong>
                </div>
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ __('محاولات لقطات الشاشة أو الطباعة') }}</span>
                    <strong style="font-size: 1.5rem; color: #dc2626; font-family: monospace;">{{ $submission->screenshots_count ?? 0 }}</strong>
                </div>
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ __('حالة نتيجة الطالب حالياً') }}</span>
                    <strong style="font-size: 0.95rem; color: {{ $submission->is_published ? '#059669' : '#d97706' }}; font-weight: 800; display: block; margin-top: 4px;">
                        {{ $submission->is_published ? __('معلنة للطالب 🟢') : __('محجوبة عن الطالب 🔒') }}
                    </strong>
                </div>
            </div>
            @if(!empty($submission->cheating_flags) && is_array($submission->cheating_flags))
                <details style="margin-top: 12px; cursor: pointer;">
                    <summary style="font-size: 0.82rem; color: #991b1b; font-weight: 700;">{{ __('عرض السجل الزمني للتنبيهات الموثقة 📋') }}</summary>
                    <ul style="margin: 8px 0 0; padding-inline-start: 20px; font-size: 0.8rem; color: #7f1d1d;">
                        @foreach($submission->cheating_flags as $flag)
                            <li>
                                <strong>{{ ($flag['type'] ?? '') == 'tab_switch' ? __('مغادرة النافذة') : __('محاولة لقطة شاشة / طباعة') }}</strong>
                                — {{ __('العدد:') }} {{ $flag['count'] ?? 1 }}
                                @if(!empty($flag['timestamp']))
                                    ({{ \Carbon\Carbon::parse($flag['timestamp'])->format('H:i:s') }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    @else
        <div style="margin-bottom: 30px; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 16px; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.6rem; color: #16a34a;">🛡️</span>
                <div>
                    <h3 style="margin: 0; color: #166534; font-size: 1.05rem; font-weight: 800;">{{ __('سجل النزاهة الأكاديمية: جلسة موثوقة ونزيهة 100%') }}</h3>
                    <p style="margin: 2px 0 0; color: #15803d; font-size: 0.82rem;">{{ __('لم يسجل النظام أي محاولات لمغادرة الصفحة أو التقاط الشاشة أثناء أداء الاختبار.') }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 0.8rem; color: #475569; font-weight: 700;">{{ __('ظهور النتيجة:') }}</span>
                <span class="chip" style="background: {{ $submission->is_published ? '#dcfce7' : '#fef3c7' }}; color: {{ $submission->is_published ? '#15803d' : '#b45309' }}; font-weight: 800; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem;">
                    {{ $submission->is_published ? __('معلنة للطالب 🟢') : __('محجوبة حتى الاعتماد 🔒') }}
                </span>
            </div>
        </div>
    @endif

    <form id="gradingForm">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 30px;">
            @foreach($submission->answers as $index => $ans)
            <div class="glass-card" style="padding: 35px; border-right: 6px solid {{ $ans->question->type == 'mcq' ? '#10b981' : '#3b82f6' }}; border-radius: 20px; background: white; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-weight: 800; color: #64748b; font-size: 0.9rem;">سؤال #{{ $index + 1 }} ({{ $ans->question->type == 'mcq' ? 'موضوعي' : 'مقالي' }})</span>
                    <span class="chip" style="background: #f1f5f9; font-weight: 800; color: #1e293b; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">الدرجة المستحقة للسؤال: {{ $ans->question->points }}</span>
                </div>

                <h3 style="font-size: 1.25rem; color: #1e293b; margin-bottom: 20px; line-height: 1.6; font-weight: 700;">{{ $ans->question->question_text }}</h3>

                @if($ans->question->image_url)
                    <div style="margin: 15px 0 20px; text-align: center; background: #f8fafc; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <img src="{{ $ans->question->image_url }}" 
                             alt="{{ __('صورة توضيحية للسؤال') }}" 
                             onclick="openZoomModal(this.src)"
                             onerror="this.onerror=null; this.parentElement.style.display='none';"
                             style="max-height: 260px; max-width: 100%; object-fit: contain; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); cursor: zoom-in; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='scale(1)'">
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> {{ __('انقر لتكبير صورة السؤال') }}
                        </div>
                    </div>
                @endif

                <div style="background: #f8fafc; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0;">
                    @if($ans->question->type == 'mcq')
                        {{-- إجابة الموضوعي --}}
                        <div style="font-weight: 700; font-size: 1.05rem;">{{ __('إجابة الطالب:') }} <span style="color: {{ $ans->answer_text == $ans->question->correct_answer ? '#059669' : '#ef4444' }};">
                                ({{ strtoupper($ans->answer_text) }}) {{ $ans->question->{$ans->answer_text} ?? $ans->answer_text }}
                            </span>
                            {!! $ans->answer_text == $ans->question->correct_answer ? ' <span style="margin-right:10px;">✅</span>' : ' <span style="margin-right:10px;">❌</span>' !!}
                        </div>
                        {{-- حقل مخفي للحفاظ على درجة الموضوعي --}}
                        <input type="hidden" name="grades[{{ $ans->id }}]" value="{{ $ans->points_awarded }}">
                    @else
                        {{-- إجابة المقالي والملفات --}}
                        <div style="margin-bottom: 20px;">
                            <strong style="display: block; margin-bottom: 10px; color: #64748b; font-size: 0.9rem;">{{ __('نص إجابة الطالب:') }}</strong>
                            <p style="line-height: 1.8; font-size: 1.05rem; color: #1e293b; background: white; padding: 15px; border-radius: 12px; border: 1px solid #eee;">
                                {{ $ans->answer_text ?? 'لا يوجد نص مقدم' }}
                            </p>

                            @if($ans->file_path)
                                <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" class="btn" style="margin-top: 15px; background: white; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px; border-radius: 12px; text-decoration: none;">
                                    <span>📂</span> فتح ملف الحل المرفق (PDF/صور)
                                </a>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px; padding-top: 20px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                            <label style="font-weight: 800; color: #0f172a;">{{ __('رصد الدرجة المستحقة:') }}</label>
                            <input type="number" name="grades[{{ $ans->id }}]"
                                   value="{{ $ans->points_awarded ?? 0 }}"
                                   max="{{ $ans->question->points }}"
                                   min="0"
                                   class="f-input"
                                   style="width: 120px; text-align: center; font-weight: 900; font-size: 1.4rem; color: #2563eb; border-color: #3b82f6;"
                                   placeholder="0">
                            <span style="color: #94a3b8; font-weight: 600;">من أصل {{ $ans->question->points }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 50px; text-align: center; padding-bottom: 80px;">
            <button type="button" onclick="submitGradesToDB()" id="submitBtn" class="btn btn-primary" style="padding: 16px 60px; font-size: 1.15rem; border-radius: 30px; background: #059669; color: white; border: none; cursor: pointer; font-weight: 800; box-shadow: 0 15px 35px rgba(5, 150, 105, 0.25); display: inline-flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>اعتماد الدرجات وإعلان النتيجة للطالب ✅</span>
            </button>
            <p style="margin-top: 10px; font-size: 0.82rem; color: #64748b;">
                {{ __('سيتم تحديث حالة التسليم إلى «تم التصحيح» ونشر الدرجة والإجابات لتظهر في لوحة الطالب فوراً.') }}
            </p>
        </div>
    </form>
</div>

<!-- Image Zoom Modal -->
<div id="imageZoomModal" onclick="closeZoomModal()" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; cursor: zoom-out;">
    <div style="position: relative; max-width: 90vw; max-height: 90vh;" onclick="event.stopPropagation()">
        <img id="zoomedImage" src="" alt="{{ __('صورة مكبرة') }}" style="max-width: 100%; max-height: 85vh; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <button type="button" onclick="closeZoomModal()" style="position: absolute; top: -15px; right: -15px; background: #ef4444; color: white; border: 2px solid white; border-radius: 50%; width: 36px; height: 36px; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            ×
        </button>
    </div>
</div>

<style>
    .f-input { padding: 12px; border-radius: 12px; border: 2px solid #f1f5f9; font-family: inherit; outline: none; transition: 0.3s; }
    .f-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 640px) {
        .glass-card {
            padding: 18px 14px !important;
            border-radius: 14px !important;
        }
        #submitBtn {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function openZoomModal(src) {
        const modal = document.getElementById('imageZoomModal');
        const img = document.getElementById('zoomedImage');
        if (modal && img) {
            img.src = src;
            modal.style.display = 'flex';
        }
    }
    function closeZoomModal() {
        const modal = document.getElementById('imageZoomModal');
        if (modal) modal.style.display = 'none';
    }

    function submitGradesToDB() {
        const btn = document.getElementById('submitBtn');
        const form = document.getElementById('gradingForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.textContent = 'جاري الحفظ والرصد...';

        // رابط حفظ ديناميكي يتعرف تلقائياً على دور المستخدِم (teacher أو admin)
        const saveRouteUrl = "{{ route(auth()->user()->role . '.submissions.saveGrade', $submission->id) }}";
        const redirectIndexUrl = "{{ route(auth()->user()->role . '.submissions.index') }}";

        axios.post(saveRouteUrl, formData)
        .then(res => {
            if(res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || 'تم اعتماد الدرجات وإعلان النتيجة بنجاح',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = redirectIndexUrl;
                });
            } else {
                Swal.fire('تنبيه!', res.data.message || 'حدث خطأ غير متوقع', 'warning');
                btn.disabled = false;
                btn.textContent = 'اعتماد الدرجات وإعلان النتيجة للطالب ✅';
            }
        })
        .catch(err => {
            console.error(err);
            const msg = err.response && err.response.data && err.response.data.message
                ? err.response.data.message
                : 'حدثت مشكلة أثناء الحفظ، يرجى المحاولة لاحقاً';
            Swal.fire('خطأ!', msg, 'error');
            btn.disabled = false;
            btn.textContent = 'اعتماد الدرجات وإعلان النتيجة للطالب ✅';
        });
    }
</script>
@endsection
