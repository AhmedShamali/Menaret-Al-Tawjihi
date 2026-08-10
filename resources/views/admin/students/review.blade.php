@extends('layouts.app')

@section('title', 'مراجعة بيانات الطالب')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; animation: fadeIn 0.8s ease;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary);">مراجعة طلب الانضمام 🔍</h1>
            <p style="color: var(--text-muted);">يرجى التأكد من مطابقة صورة الهوية مع البيانات المدخلة.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="approveStudent({{ $student->id }})" class="btn btn-primary" style="background: #059669; padding: 12px 30px;">✅ اعتماد وتفعيل</button>
            <button onclick="rejectStudent({{ $student->id }})" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 12px 30px;">❌ رفض الطلب</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">

        <!-- كرت الصور (المرفقات) -->
        <div class="glass-card" style="padding: 35px; text-align: center;">
            <h3 style="font-size: 1.1rem; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">🖼️ الوثائق المرفوعة</h3>

            <div style="margin-bottom: 30px;">
                <span style="display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 10px;">الصورة الشخصية</span>
                <img src="{{ asset('storage/'.$student->photo) }}" style="width: 200px; height: 200px; border-radius: 30px; object-fit: cover; border: 5px solid #f8fafc; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            </div>

            <div>
                <span style="display: block; font-size: 0.8rem; color: #94a3b8; margin-bottom: 10px;">صورة بطاقة الهوية</span>
                <img src="{{ asset('storage/'.$student->id_photo) }}" style="width: 100%; border-radius: 15px; border: 1px solid #e2e8f0; cursor: zoom-in;" onclick="window.open(this.src)">
            </div>
        </div>

        <!-- كرت البيانات النصية -->
        <div class="glass-card" style="padding: 35px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">📑 البيانات الرسمية</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="info-box"><span>الاسم العربي:</span> <strong>{{ $student->name_ar }}</strong></div>
                <div class="info-box"><span>الاسم الإنجليزي:</span> <strong>{{ $student->name_en }}</strong></div>
                <div class="info-box"><span>رقم الهوية:</span> <strong style="font-family: monospace; letter-spacing: 2px;">{{ $student->nid }}</strong></div>
                <div class="info-box"><span>العمر:</span> <strong>{{ $student->age }} عام</strong></div>
                <div class="info-box"><span>الجنس:</span> <strong>{{ $student->gender }}</strong></div>
                <div class="info-box"><span>المرحلة:</span> <strong class="chip" style="background: var(--accent); color: white;">{{ $student->stage->label_ar }}</strong></div>
                <div class="info-box"><span>الجوال:</span> <strong dir="ltr">{{ $student->phone }}</strong></div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 15px; font-size: 0.9rem; }
    .info-box span { color: #64748b; font-weight: 600; }
    .info-box strong { color: var(--primary); }
</style>
@endsection
