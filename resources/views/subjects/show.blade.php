@extends('layouts.app')

@section('title', $subject->name_ar)

@section('content')
<div style="display: flex; flex-direction: column; gap: 30px;">

    <!-- هيدر المادة -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <nav style="display: flex; gap: 8px; font-size: 0.8rem; color: var(--text-light); margin-bottom: 10px;">
                <a href="/stages" style="color: inherit; text-decoration: none;">المراحل</a> /
                <a href="/stages/{{ $subject->stage_id }}" style="color: inherit; text-decoration: none;">{{ $subject->stage->label_ar }}</a> /
                <span style="color: var(--accent); font-weight: 600;">{{ $subject->name_ar }}</span>
            </nav>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">{{ $subject->icon }} مادة {{ $subject->name_ar }}</h1>
        </div>
        <div style="display: flex; gap: 12px;">
            <button class="btn" style="background: #f1f5f9; color: var(--primary); font-weight: 700;">📑 ملخصات</button>
            <button class="btn btn-primary" style="background: var(--tatreez);">🤖 المساعد الذكي</button>
        </div>
    </div>

    <!-- المحتوى التعليمي (Grid) -->
    <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 30px;">

        <!-- العمود الأيمن: الفيديوهات -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 1.3rem; font-weight: 700;">🎥 دروس الفيديو الشارحة</h3>
                <span class="chip chip-emerald">2 فيديو متوفر</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                {{-- مثال لفيديو (هذه البيانات ستأتي لاحقاً من جدول المحتوى) --}}
                <div class="glass-card" style="padding: 0; overflow: hidden; border: none;">
                    <div style="aspect-ratio: 16/9; background: #000; position: relative;">
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/v8vI-Xp_6XU" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div style="padding: 20px;">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--primary);">شرح مقدمة المنهج</h4>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-top: 5px;">قناة التعليم الفلسطيني الرقمي</p>
                    </div>
                </div>

                <div class="glass-card" style="padding: 0; overflow: hidden; border: none;">
                    <div style="aspect-ratio: 16/9; background: #000;">
                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div style="padding: 20px;">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--primary);">حل تمارين الوحدة الأولى</h4>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-top: 5px;">مراجعة نهائية</p>
                    </div>
                </div>
            </div>

            <!-- اختبار تفاعلي -->
            <div class="glass-card" style="background: var(--primary); color: white; border: none; padding: 40px; display: flex; align-items: center; justify-content: space-between;">
                <div style="max-width: 60%;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px;">📝 اختبار المادة الذكي</h3>
                    <p style="opacity: 0.7; font-size: 0.95rem;">هل أنت مستعد لاختبار معلوماتك؟ ابدأ الآن الاختبار التفاعلي لتحصل على تقييم فوري لمستواك.</p>
                </div>
                <button class="btn btn-primary" style="padding: 15px 30px;">ابدأ الاختبار</button>
            </div>
        </div>

        <!-- العمود الأيسر: الكتب والتقدم -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <div class="glass-card">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px;">📘 المصادر الورقية</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="file-row">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.2rem;">📄</span>
                            <div>
                                <div style="font-weight: 600; font-size: 0.85rem;">كتاب الوزارة الرسمي</div>
                                <div style="font-size: 0.7rem; color: var(--text-light);">PDF - 12MB</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary" style="padding: 5px 12px; font-size: 0.7rem;">تحميل</a>
                    </div>

                    <div class="file-row">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.2rem;">📝</span>
                            <div>
                                <div style="font-weight: 600; font-size: 0.85rem;">ملخص الوحدة الأولى</div>
                                <div style="font-size: 0.7rem; color: var(--text-light);">PDF - 4MB</div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary" style="padding: 5px 12px; font-size: 0.7rem;">تحميل</a>
                    </div>
                </div>
            </div>

            <div class="glass-card" style="background: #ecfdf5; border-color: #d1fae5;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #065f46; margin-bottom: 10px;">📊 حالة التقدّم</h3>
                <div style="background: #fff; height: 10px; border-radius: 10px; margin-top: 15px; overflow: hidden;">
                    <div style="width: 35%; height: 100%; background: var(--accent);"></div>
                </div>
                <p style="font-size: 0.75rem; color: #059669; font-weight: 600; margin-top: 10px;">لقد أكملت 35% من هذه المادة</p>
            </div>

        </div>
    </div>
</div>

<style>
    .file-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px; background: var(--bg); border-radius: 14px;
    }
</style>
@endsection
