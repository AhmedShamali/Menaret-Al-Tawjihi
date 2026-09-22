@extends('layouts.app')

@section('title', __('الملف الشخصي') . ' | ' . ($student->name_ar ?? auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب')))

@section('content')
<div class="ed-profile-container">

    <!-- ترويسة الصفحة الكلاسيكية -->
    <div class="ed-profile-header">
        <div>
            <h1 class="ed-profile-title">
                {{ __('ملفي الشخصي وبيانات الطالب') }}
            </h1>
            <p class="ed-profile-subtitle">
                {{ __('متابعة إعدادات الحساب، مسارك في توجيهي فلسطين، ودرع الالتزام اليومي') }}
            </p>
        </div>
        <div>
            <a href="{{ route('student.dashboard') }}" class="ed-btn-home">
                <i class="fa-solid fa-arrow-right arrow-icon"></i>
                <span>{{ __('العودة للرئيسية') }}</span>
            </a>
        </div>
    </div>

    <div class="ed-profile-layout">

        <!-- الجانب الأول: بطاقة السجل الأكاديمي للطالب -->
        <aside class="ed-profile-card">
            <div class="ed-avatar-wrapper">
                @php
                    $photoPath = $student->photo ?? null;
                    $fullPath = $photoPath ? public_path('storage/' . $photoPath) : null;
                @endphp

                @if(!empty($photoPath) && file_exists($fullPath))
                    <img src="{{ asset('storage/' . $photoPath) }}" class="ed-student-photo" alt="{{ $student->name_ar ?? 'طالب' }}">
                @else
                    <div class="ed-student-avatar-fallback">
                        👨‍🎓
                    </div>
                @endif
                <div class="ed-verified-badge" title="{{ __('نشط') }}">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h2 class="ed-student-name">{{ $student->name_ar ?? auth('student')->user()?->name_ar ?? auth()->user()?->name ?? __('طالب') }}</h2>
            <div class="ed-stage-badge">
                <i class="fa-solid fa-flag"></i>
                <span>{{ optional(optional($student)->stage)->label_ar ?? __('الثانوية العامة - فلسطين') }}</span>
            </div>

            <!-- إحصائيات الالتزام الكلاسيكية -->
            <div class="ed-kpi-row">
                <div class="ed-kpi-box orange">
                    <div class="kpi-val">
                        <span>{{ $student->streak_count ?? 1 }}</span>
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <span class="kpi-lbl">{{ __('أيام متتالية') }}</span>
                </div>
                <div class="ed-kpi-box green">
                    <div class="kpi-val">
                        <span>{{ $student->total_points ?? 50 }}</span>
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <span class="kpi-lbl">{{ __('نقاط التميز') }}</span>
                </div>
            </div>

            <!-- تفاصيل الحساب الأكاديمي -->
            <div class="ed-details-list">
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-id-card"></i> {{ __('رقم الهوية:') }}</span>
                    <strong class="detail-value font-mono">{{ $student->nid ?? __('غير مسجل') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-location-dot"></i> {{ __('المدينة / المحافظة:') }}</span>
                    <strong class="detail-value">{{ $student->city ?? __('فلسطين') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-school"></i> {{ __('المدرسة:') }}</span>
                    <strong class="detail-value">{{ $student->school_name ?? __('غير محددة') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-envelope"></i> {{ __('البريد:') }}</span>
                    <strong class="detail-value font-mono text-sm">{{ $student->email ?? auth('student')->user()?->email ?? auth()->user()?->email ?? '---' }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-phone"></i> {{ __('الجوال:') }}</span>
                    <strong class="detail-value font-mono">{{ $student->phone ?? __('غير متوفر') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-user-shield"></i> {{ __('جوال ولي الأمر:') }}</span>
                    <strong class="detail-value font-mono">{{ $student->guardian_phone ?? __('غير متوفر') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-brands fa-whatsapp"></i> {{ __('واتساب:') }}</span>
                    <strong class="detail-value font-mono">{{ $student->whatsapp ?? $student->phone ?? __('غير متوفر') }}</strong>
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-address-card"></i> {{ __('بطاقة الهوية:') }}</span>
                    @if(!empty($student->id_photo))
                        <span class="ed-badge-status green"><i class="fa-solid fa-circle-check"></i> {{ __('مرفقة ومعتمدة') }}</span>
                    @else
                        <span class="ed-badge-status red"><i class="fa-solid fa-circle-exclamation"></i> {{ __('غير مرفقة') }}</span>
                    @endif
                </div>
                <div class="ed-detail-row">
                    <span class="detail-label"><i class="fa-solid fa-calendar-check"></i> {{ __('تاريخ الانضمام:') }}</span>
                    <strong class="detail-value font-mono">{{ $student->created_at ? $student->created_at->format('Y/m/d') : __('حديثاً') }}</strong>
                </div>
            </div>
        </aside>

        <!-- الجانب الثاني: اختصارات الأدوات وتحديث كلمة المرور -->
        <main class="ed-profile-main">

            <!-- اختصارات أدوات التوجيهي الكلاسيكية -->
            <div class="ed-card-section">
                <h3 class="ed-section-heading">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>{{ __('أدواتي الدراسية النشطة') }}</span>
                </h3>

                <div class="ed-tools-grid">
                    <a href="{{ route('student.subjects.index') }}" class="ed-tool-item">
                        <div class="tool-icon blue"><i class="fa-solid fa-book-open"></i></div>
                        <div>
                            <strong>{{ __('مناهجي ومقرراتي') }}</strong>
                            <small>{{ __('المواد والدروس') }}</small>
                        </div>
                    </a>

                    <a href="{{ route('student.planner.index') }}" class="ed-tool-item">
                        <div class="tool-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
                        <div>
                            <strong>{{ __('جدول المراجعة') }}</strong>
                            <small>{{ __('خطة دراسية للأيام المتبقية') }}</small>
                        </div>
                    </a>

                    <a href="{{ route('tawjihi.calculator') }}" target="_blank" class="ed-tool-item">
                        <div class="tool-icon green"><i class="fa-solid fa-calculator"></i></div>
                        <div>
                            <strong>{{ __('حاسبة المعدل') }}</strong>
                            <small>{{ __('دليل التنسيق والقبول الجامعي') }}</small>
                        </div>
                    </a>

                    <a href="{{ route('student.planner.index') }}" class="ed-tool-item">
                        <div class="tool-icon purple"><i class="fa-solid fa-calendar-check"></i></div>
                        <div>
                            <strong>{{ __('جدول المراجعة والمذاكرة') }}</strong>
                            <small>{{ __('خطة دراسية وتنظيم الوقت اليومي') }}</small>
                        </div>
                    </a>
                </div>
            </div>

            <!-- كرت تغيير كلمة المرور والأمان -->
            <div class="ed-card-section">
                <h3 class="ed-section-heading">
                    <i class="fa-solid fa-shield-halved text-green"></i>
                    <span>{{ __('أمان الحساب وتغيير كلمة المرور') }}</span>
                </h3>

                <form id="profilePassForm" onsubmit="handlePasswordUpdate(event)">
                    @csrf
                    <div class="ed-form-grid">
                        <div class="ed-form-field">
                            <label>{{ __('كلمة المرور الحالية') }}</label>
                            <input type="password" id="old_password" name="old_password" required placeholder="••••••••" class="ed-input">
                        </div>
                        <div class="ed-form-field">
                            <label>{{ __('كلمة المرور الجديدة') }}</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="{{ __('لا تقل عن 6 خانات') }}" class="ed-input">
                        </div>
                    </div>

                    <div class="ed-form-footer">
                        <button type="submit" id="btnUpdatePass" class="ed-btn-submit">
                            <i class="fa-solid fa-lock"></i>
                            <span>{{ __('حفظ وتحديث كلمة المرور') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- الدعم والمساعدة -->
            <div class="ed-support-banner">
                <div>
                    <h4>{{ __('هل تحتاج لتعديل فرعك أو بياناتك الرسمية؟') }}</h4>
                    <p>{{ __('تواصل مع فريق الدعم الفني والإرشاد التربوي لمساعدتك على الفور.') }}</p>
                </div>
                <a href="{{ route('student.support') }}" class="ed-btn-support">
                    <i class="fa-solid fa-headset"></i>
                    <span>{{ __('محادثة الدعم') }}</span>
                </a>
            </div>

        </main>
    </div>
</div>

<style>
/* ==========================================================
   CLASSIC ACADEMIC STUDENT PROFILE STYLES (100% RESPONSIVE)
   ========================================================== */
.ed-profile-container {
    width: 100%;
    margin: 0;
    padding: 0 0 60px;
    box-sizing: border-box;
}

.ed-profile-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-profile-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}

.ed-profile-subtitle {
    font-size: 0.86rem;
    color: #64748b;
    margin: 0;
}

.ed-btn-home {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #334155;
    text-decoration: none;
    transition: 0.15s;
}

.ed-btn-home:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.ed-profile-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
    align-items: start;
}

/* Sidebar Card */
.ed-profile-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 28px 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 14px;
}

.ed-student-photo {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    object-fit: cover;
    border: 3px solid #eff6ff;
}

.ed-student-avatar-fallback {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 2.5rem;
    border: 3px solid #bfdbfe;
    margin: 0 auto;
}

.ed-verified-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 26px;
    height: 26px;
    background: #16a34a;
    color: #ffffff;
    border-radius: 50%;
    display: grid;
    place-items: center;
    border: 2px solid #ffffff;
    font-size: 0.75rem;
}

.ed-student-name {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 3px 12px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
    margin-bottom: 18px;
}

.ed-kpi-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}

.ed-kpi-box {
    border-radius: 8px;
    padding: 10px 8px;
    text-align: center;
}

.ed-kpi-box.orange {
    background: #fff7ed;
    border: 1px solid #fed7aa;
}

.ed-kpi-box.green {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}

.kpi-val {
    font-size: 1.2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-family: monospace;
}

.ed-kpi-box.orange .kpi-val { color: #ea580c; }
.ed-kpi-box.green .kpi-val { color: #16a34a; }

.kpi-lbl {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    display: block;
    margin-top: 2px;
}

.ed-details-list {
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ed-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.82rem;
}

.detail-label {
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.detail-label i { font-size: 0.85rem; color: #94a3b8; }

.detail-value {
    color: #0f172a;
    font-weight: 700;
}

.text-sm { font-size: 0.76rem; }

.ed-badge-status {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.ed-badge-status.green { background: #dcfce7; color: #15803d; }
.ed-badge-status.red { background: #fee2e2; color: #b91c1c; }

/* Main sections */
.ed-profile-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.ed-card-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-section-heading {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ed-section-heading i { color: #1e3a8a; }
.ed-section-heading .text-green { color: #16a34a; }

.ed-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}

.ed-tool-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    color: #1e293b;
    transition: 0.15s;
}

.ed-tool-item:hover {
    border-color: #1e3a8a;
    background: #ffffff;
}

.tool-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.tool-icon.blue { background: #eff6ff; color: #1e3a8a; }
.tool-icon.green { background: #f0fdf4; color: #16a34a; }
.tool-icon.red { background: #fee2e2; color: #dc2626; }

.ed-tool-item strong {
    font-size: 0.84rem;
    display: block;
    color: #0f172a;
}

.ed-tool-item small {
    font-size: 0.72rem;
    color: #64748b;
}

/* Password Form */
.ed-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.ed-form-field label {
    display: block;
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
}

.ed-input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-size: 0.88rem;
    box-sizing: border-box;
    outline: none;
    transition: 0.15s;
}

.ed-input:focus {
    border-color: #1e3a8a;
    background: #ffffff;
}

.ed-form-footer {
    margin-top: 18px;
    display: flex;
    justify-content: flex-end;
}

.ed-btn-submit {
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.86rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.15s;
}

.ed-btn-submit:hover {
    background: #172554;
}

/* Support banner */
.ed-support-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
}

.ed-support-banner h4 {
    margin: 0 0 4px;
    font-size: 0.92rem;
    font-weight: 800;
    color: #0f172a;
}

.ed-support-banner p {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
}

.ed-btn-support {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 8px 18px;
    border-radius: 8px;
    color: #1e3a8a;
    font-weight: 700;
    font-size: 0.84rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

html[dir="ltr"] .arrow-icon {
    transform: rotate(180deg);
}

@media (max-width: 860px) {
    .ed-profile-layout {
        grid-template-columns: 1fr;
    }
    .ed-form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function handlePasswordUpdate(e) {
    e.preventDefault();
    const btn = document.getElementById('btnUpdatePass');
    const oldPass = document.getElementById('old_password').value;
    const newPass = document.getElementById('new_password').value;

    if (!oldPass || !newPass) {
        Swal.fire({ icon: 'warning', title: '{{ __("تنبيه") }}', text: '{{ __("يرجى ملء كافة الحقول المطلوبة") }}' });
        return;
    }

    if (newPass.length < 6) {
        Swal.fire({ icon: 'warning', title: '{{ __("تنبيه") }}', text: '{{ __("كلمة المرور الجديدة يجب أن لا تقل عن 6 خانات.") }}' });
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري المعالجة...") }}';

    axios.post('{{ route("student.profile.updatePassword") }}', {
        old_password: oldPass,
        new_password: newPass
    })
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock"></i> <span>{{ __("حفظ وتحديث كلمة المرور") }}</span>';
        document.getElementById('profilePassForm').reset();

        Swal.fire({
            icon: 'success',
            title: '{{ __("تم بنجاح") }}',
            text: res.data.message || '{{ __("تم تحديث كلمة المرور الخاصة بك بنجاح.") }}',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock"></i> <span>{{ __("حفظ وتحديث كلمة المرور") }}</span>';
        const msg = err.response?.data?.message || err.response?.data?.title || '{{ __("تعذر تحديث كلمة المرور، يرجى التأكد من كلمة المرور الحالية.") }}';
        Swal.fire({
            icon: 'error',
            title: '{{ __("خطأ") }}',
            text: msg
        });
    });
}
</script>
@endsection
