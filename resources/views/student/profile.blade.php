@extends('layouts.app')

@section('title', 'ملفي الشخصي')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 30px; align-items: start;">

        <!-- الجانب الأيمن: كرت التعريف -->
        <aside class="glass-card" style="padding: 40px; text-align: center; border: none; background: white;">
            <div style="position: relative; display: inline-block;">
                @php
                    $photoPath = $student->photo ?? null;
                    $fullPath = public_path('storage/' . $photoPath);
                @endphp

                @if(!empty($photoPath) && $photoPath != 'default.png' && file_exists($fullPath))
                    <img src="{{ asset('storage/' . $photoPath) }}" style="width: 130px; height: 130px; border-radius: 40px; object-fit: cover; border: 5px solid #f8fafc; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                @else
                    <div style="width: 130px; height: 130px; border-radius: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; border: 5px solid #f8fafc; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin: 0 auto;">
                        👤
                    </div>
                @endif
                <div style="position: absolute; bottom: 5px; right: 5px; width: 30px; height: 30px; background: var(--accent); color: white; border-radius: 50%; display: grid; place-items: center; border: 3px solid white; font-size: 0.8rem;">🎓</div>
            </div>

            <h2 style="margin-top: 20px; font-size: 1.4rem; font-weight: 800; color: var(--primary);">{{ $student->name_ar ?? auth()->user()->name }}</h2>
            <span class="chip" style="background: #ecfdf5; color: #059669; margin-top: 10px;">{{ optional(optional($student)->stage)->label_ar ?? 'طالب' }}</span>
            <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #f1f5f9; text-align: right; display: flex; flex-direction: column; gap: 15px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #94a3b8;">المرحلة الدراسية:</span>
                    <strong>{{ $student?->stage?->label_ar ?? 'غير محددة' }}</strong>                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #94a3b8;">رقم الهوية:</span>
                    <strong>{{ $student->nid ?? 'غير متوفر' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #94a3b8;">البريد:</span>
                    <strong style="font-size: 0.75rem;">{{ $student->email ?? auth()->user()->email }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                    <span style="color: #94a3b8;">الجوال:</span>
                    <strong>{{ $student->phone ?? 'غير متوفر' }}</strong>
                </div>
            </div>
        </aside>

        <!-- الجانب الأيسر: الإعدادات والنشاط -->
        <main style="display: flex; flex-direction: column; gap: 30px;">

            {{-- كرت الأمان --}}
            <div class="glass-card" style="padding: 40px; border: none; background: white;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-bottom: 25px; border-right: 4px solid var(--accent); padding-right: 15px;">إعدادات الحساب والأمان</h3>
                <form id="profilePassForm">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display:block; font-size:0.8rem; font-weight:700; color:#64748b; margin-bottom:10px;">كلمة المرور القديمة</label>
                            <input type="password" name="old_password" class="f-input" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.8rem; font-weight:700; color:#64748b; margin-bottom:10px;">كلمة المرور الجديدة</label>
                            <input type="password" name="new_password" class="f-input" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" style="margin-top: 25px; padding: 12px 30px; border-radius: 12px;">تحديث البيانات ✅</button>
                </form>
            </div>

            {{-- كرت سجل النشاطات --}}
            <div class="glass-card" style="padding: 40px; border: none; background: white;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-bottom: 25px; border-right: 4px solid #3b82f6; padding-right: 15px;">سجل نشاطاتي الأخير</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @isset($activities)
                        @forelse($activities as $act)
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border-radius: 15px;">
                                <div style="width: 40px; height: 40px; background: white; border-radius: 10px; display: grid; place-items: center;">{{ $act->type == 'exam' ? '📝' : '🎥' }}</div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; font-size: 0.9rem;">{{ $act->description }}</div>
                                    <small style="color: #94a3b8;">{{ $act->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p style="text-align: center; color: #94a3b8;">لا يوجد نشاطات مسجلة بعد.</p>
                        @endforelse
                    @else
                        <p style="text-align: center; color: #94a3b8;">لا يوجد نشاطات مسجلة بعد.</p>
                    @endisset
                </div>
            </div>

        </main>
    </div>
</div>
@endsection
