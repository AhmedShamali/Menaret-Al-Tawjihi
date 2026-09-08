@extends('layouts.app')

@section('title', 'ملفي الشخصي | ' . ($student->name_ar ?? auth()->user()->name ?? 'طالب'))

@section('content')
<div style="max-width: 1100px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <!-- ترويسة الصفحة -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                ملفي الشخصي وبيانات الطالب 🎓
            </h1>
            <p style="color: #64748b; font-size: 0.88rem;">متابعة إعدادات الحساب، مسارك في توجيهي فلسطين، ودرع الالتزام اليومي</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('student.dashboard') }}" style="padding: 10px 20px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.85rem; font-weight: 700; color: #1e293b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 340px 1fr; gap: 25px; align-items: start;">

        <!-- الجانب الأيمن: كرت التعريف والدرع -->
        <aside style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 35px 25px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <div style="position: relative; display: inline-block; margin-bottom: 18px;">
                @php
                    $photoPath = $student->photo ?? null;
                    $fullPath = $photoPath ? public_path('storage/' . $photoPath) : null;
                @endphp

                @if(!empty($photoPath) && file_exists($fullPath))
                    <img src="{{ asset('storage/' . $photoPath) }}" style="width: 125px; height: 125px; border-radius: 35px; object-fit: cover; border: 4px solid #f0f9ff; box-shadow: 0 8px 24px rgba(2,132,199,0.15);">
                @else
                    <div style="width: 125px; height: 125px; border-radius: 35px; background: linear-gradient(135deg, #0284c7, #6366f1); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; border: 4px solid #f0f9ff; box-shadow: 0 8px 24px rgba(2,132,199,0.15); margin: 0 auto;">
                        👨‍🎓
                    </div>
                @endif
                <div style="position: absolute; bottom: -2px; right: -2px; width: 34px; height: 34px; background: #10b981; color: white; border-radius: 50%; display: grid; place-items: center; border: 3px solid white; font-size: 0.9rem;" title="حساب مفعل وموثق">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">{{ $student->name_ar ?? auth()->user()->name }}</h2>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #0284c7; padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 20px;">
                <i class="fa-solid fa-flag"></i> {{ optional(optional($student)->stage)->label_ar ?? 'توجيهي فلسطين' }}
            </div>

            <!-- إحصائيات سريعة للالتزام -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 25px;">
                <div style="background: #fff7ed; border: 1px solid #ffedd5; padding: 14px; border-radius: 14px; text-align: center;">
                    <div style="font-size: 1.4rem; font-weight: 800; color: #ea580c; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <span>{{ $student->streak_count ?? 1 }}</span>
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <span style="font-size: 0.75rem; color: #9a3412; font-weight: 700;">أيام متتالية 🔥</span>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 14px; border-radius: 14px; text-align: center;">
                    <div style="font-size: 1.4rem; font-weight: 800; color: #16a34a; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <span>{{ $student->total_points ?? 50 }}</span>
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <span style="font-size: 0.75rem; color: #166534; font-weight: 700;">نقاط التميز 🏆</span>
                </div>
            </div>

            <!-- تفاصيل الحساب الأكاديمي -->
            <div style="text-align: right; border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #64748b; font-weight: 600;"><i class="fa-solid fa-id-card" style="margin-left: 6px; color: #94a3b8;"></i> رقم الهوية:</span>
                    <strong style="color: #0f172a; font-family: monospace; font-size: 0.95rem;">{{ $student->nid ?? 'غير مسجل' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #64748b; font-weight: 600;"><i class="fa-solid fa-envelope" style="margin-left: 6px; color: #94a3b8;"></i> البريد:</span>
                    <strong style="color: #0f172a; font-size: 0.82rem;">{{ $student->email ?? auth()->user()->email }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #64748b; font-weight: 600;"><i class="fa-solid fa-phone" style="margin-left: 6px; color: #94a3b8;"></i> الجوال:</span>
                    <strong style="color: #0f172a; font-family: monospace;">{{ $student->phone ?? 'غير متوفر' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #64748b; font-weight: 600;"><i class="fa-brands fa-whatsapp" style="margin-left: 6px; color: #10b981;"></i> واتساب:</span>
                    <strong style="color: #0f172a; font-family: monospace;">{{ $student->whatsapp ?? $student->phone ?? 'غير متوفر' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #64748b; font-weight: 600;"><i class="fa-solid fa-calendar-check" style="margin-left: 6px; color: #94a3b8;"></i> تاريخ الانضمام:</span>
                    <strong style="color: #0f172a;">{{ $student->created_at ? $student->created_at->format('Y/m/d') : 'حديثاً' }}</strong>
                </div>
            </div>
        </aside>

        <!-- الجانب الأيسر: إعدادات الأمان وأدوات التوجيهي السريعة -->
        <main style="display: flex; flex-direction: column; gap: 25px;">

            <!-- اختصارات أدوات التوجيهي -->
            <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 25px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-wand-magic-sparkles" style="color: #0284c7;"></i>
                    أدواتي الدراسية النشطة
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
                    <a href="{{ route('student.flashcards.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; text-decoration: none; color: #1e293b; transition: 0.2s;" onmouseover="this.style.borderColor='#6366f1'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ede9fe; color: #7c3aed; display: grid; place-items: center; font-size: 1.1rem;"><i class="fa-solid fa-layer-group"></i></div>
                        <div>
                            <strong style="font-size: 0.85rem; display: block;">بطاقات الاستذكار</strong>
                            <small style="color: #64748b; font-size: 0.72rem;">مراجعة وحفظ</small>
                        </div>
                    </a>

                    <a href="{{ route('student.planner.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; text-decoration: none; color: #1e293b; transition: 0.2s;" onmouseover="this.style.borderColor='#0284c7'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.1rem;"><i class="fa-solid fa-calendar-days"></i></div>
                        <div>
                            <strong style="font-size: 0.85rem; display: block;">جدول المراجعة</strong>
                            <small style="color: #64748b; font-size: 0.72rem;">تنظيم جدول الدراسة</small>
                        </div>
                    </a>

                    <a href="{{ route('tawjihi.calculator') }}" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; text-decoration: none; color: #1e293b; transition: 0.2s;" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #d1fae5; color: #059669; display: grid; place-items: center; font-size: 1.1rem;"><i class="fa-solid fa-calculator"></i></div>
                        <div>
                            <strong style="font-size: 0.85rem; display: block;">حاسبة المعدل</strong>
                            <small style="color: #64748b; font-size: 0.72rem;">تنسيق الجامعات</small>
                        </div>
                    </a>

                    <a href="{{ route('tawjihi.archive') }}" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; text-decoration: none; color: #1e293b; transition: 0.2s;" onmouseover="this.style.borderColor='#f43f5e'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ffe4e6; color: #e11d48; display: grid; place-items: center; font-size: 1.1rem;"><i class="fa-solid fa-file-invoice"></i></div>
                        <div>
                            <strong style="font-size: 0.85rem; display: block;">بنك الامتحانات</strong>
                            <small style="color: #64748b; font-size: 0.72rem;">نماذج الإجابة الوزارية</small>
                        </div>
                    </a>
                </div>
            </div>

            <!-- كرت تغيير كلمة المرور والأمان -->
            <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i>
                    أمان الحساب وتغيير كلمة المرور
                </h3>

                <form id="profilePassForm" onsubmit="handlePasswordUpdate(event)">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:8px;">كلمة المرور الحالية</label>
                            <input type="password" id="old_password" name="old_password" required placeholder="••••••••" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 0.9rem; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#0284c7'">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:8px;">كلمة المرور الجديدة</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="لا تقل عن 6 خانات" style="width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 0.9rem; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='#0284c7'">
                        </div>
                    </div>

                    <div style="margin-top: 22px; display: flex; justify-content: flex-end;">
                        <button type="submit" id="btnUpdatePass" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border: none; padding: 12px 28px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(2,132,199,0.25); transition: 0.2s;">
                            <i class="fa-solid fa-lock"></i>
                            <span>حفظ وتحديث كلمة المرور</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- الدعم والمساعدة -->
            <div style="background: #f8fafc; border-radius: 18px; border: 1px solid #e2e8f0; padding: 22px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">هل تحتاج لتعديل فرعك أو بياناتك الرسمية؟</h4>
                    <p style="font-size: 0.82rem; color: #64748b;">تواصل مع فريق الدعم الفني والإرشاد التربوي لمساعدتك على الفور.</p>
                </div>
                <a href="{{ route('student.support') }}" style="padding: 10px 22px; background: white; border: 1px solid #cbd5e1; border-radius: 12px; color: #0284c7; text-decoration: none; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-headset"></i> محادثة الدعم
                </a>
            </div>

        </main>
    </div>
</div>

<script>
function handlePasswordUpdate(e) {
    e.preventDefault();
    const btn = document.getElementById('btnUpdatePass');
    const oldPass = document.getElementById('old_password').value;
    const newPass = document.getElementById('new_password').value;

    if (!oldPass || !newPass) {
        Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى ملء حقلي كلمة المرور.' });
        return;
    }

    if (newPass.length < 6) {
        Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'كلمة المرور الجديدة يجب أن لا تقل عن 6 خانات.' });
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحديث...';

    axios.post('{{ route("student.profile.updatePassword") }}', {
        old_password: oldPass,
        new_password: newPass
    })
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock"></i> <span>حفظ وتحديث كلمة المرور</span>';
        document.getElementById('profilePassForm').reset();

        Swal.fire({
            icon: 'success',
            title: 'تم التحديث بنجاح!',
            text: res.data.message || 'تم تحديث كلمة المرور الخاصة بك بنجاح.',
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock"></i> <span>حفظ وتحديث كلمة المرور</span>';
        const msg = err.response?.data?.message || err.response?.data?.title || 'تعذر تحديث كلمة المرور، يرجى التأكد من كلمة المرور الحالية.';
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: msg
        });
    });
}
</script>
@endsection
