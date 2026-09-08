@extends('layouts.app')

@section('title', 'فروع الثانوية العامة (التوجيهي) | المنهاج الفلسطيني')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 10px 0 60px;">

    <!-- Header Banner -->
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; padding: 6px 16px; border-radius: 999px; font-size: 0.85rem; font-weight: 700; margin-bottom: 14px;">
            <span>🇵🇸</span>
            <span>المنهاج الفلسطيني الرسمي المعتمد 2026</span>
        </div>
        <h1 style="font-size: 2.3rem; font-weight: 800; color: #0f172a; margin-bottom: 12px; letter-spacing: -0.5px;">
            فروع الثانوية العامة (التوجيهي)
        </h1>
        <p style="color: #64748b; font-size: 1.05rem; max-width: 650px; margin: 0 auto; line-height: 1.7;">
            اختر مسارك الدراسي للوصول إلى شروحات المباحث المقررة، أوزان العلامات الوزارية المعتمدة، وبنوك الأسئلة والدروس التفاعلية.
        </p>
    </div>

    <!-- Tawjihi Branches Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 28px;">
        @forelse($stages as $stage)
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 32px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04); transition: transform 0.2s, box-shadow 0.2s;">
            <div>
                <!-- Top Card Info -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px;">
                    <div style="width: 65px; height: 65px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; display: grid; place-items: center; font-size: 2.2rem;">
                        {{ $stage->icon ?? '🎓' }}
                    </div>
                    <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.82rem; font-weight: 700; padding: 6px 14px; border-radius: 8px;">
                        {{ $stage->subjects_count ?? $stage->subjects->count() }} مواد وزارية
                    </span>
                </div>

                <!-- Branch Title -->
                <h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin-bottom: 10px;">
                    {{ $stage->label_ar }}
                </h2>

                <!-- Special Grade Rules Indicator -->
                @if($stage->grade_level == 122)
                    <div style="background: #f8fafc; border-right: 3px solid #1e40af; padding: 8px 12px; border-radius: 6px; font-size: 0.82rem; color: #334155; margin-bottom: 18px;">
                        <strong>سلم الدرجات:</strong> الرياضيات من <strong>200</strong> علامة | باقي المباحث من <strong>100</strong> (المجموع 700)
                    </div>
                @elseif($stage->grade_level == 121)
                    <div style="background: #f8fafc; border-right: 3px solid #b45309; padding: 8px 12px; border-radius: 6px; font-size: 0.82rem; color: #334155; margin-bottom: 18px;">
                        <strong>سلم الدرجات:</strong> العربي والإنجليزي من <strong>150</strong> علامة | باقي المباحث من <strong>100</strong> (المجموع 700)
                    </div>
                @else
                    <div style="background: #f8fafc; border-right: 3px solid #059669; padding: 8px 12px; border-radius: 6px; font-size: 0.82rem; color: #334155; margin-bottom: 18px;">
                        <strong>سلم الدرجات:</strong> المباحث التخصصية والاختيارية من <strong>100</strong> علامة لكل مبحث
                    </div>
                @endif

                <!-- Subjects Chips Preview -->
                <p style="font-size: 0.82rem; font-weight: 700; color: #64748b; margin-bottom: 8px;">المباحث المقررة:</p>
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 26px;">
                    @forelse($stage->subjects->take(6) as $sub)
                        <span style="background: #f1f5f9; color: #1e293b; font-size: 0.78rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            {{ $sub->name_ar }}
                        </span>
                    @empty
                        <span style="font-size: 0.8rem; color: #94a3b8;">جاري تحميل المباحث...</span>
                    @endforelse
                    @if($stage->subjects->count() > 6)
                        <span style="background: #e2e8f0; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border-radius: 6px;">
                            +{{ $stage->subjects->count() - 6 }} إضافية
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Button -->
            <div>
                <a href="{{ route('stages.show', $stage->id) }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #1e40af; color: #ffffff; text-decoration: none; padding: 13px; border-radius: 12px; font-size: 0.95rem; font-weight: 700; transition: background 0.2s;">
                    <span>استكشاف مباحث الفرع</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;">
            <p style="color: #64748b; font-size: 1.1rem;">جاري تحميل فروع الثانوية العامة المعتمدة...</p>
        </div>
        @endforelse
    </div>

    <!-- Quick Tawjihi Calculator Link -->
    <div style="margin-top: 50px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 28px 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #1e40af; display: grid; place-items: center; font-size: 1.4rem;">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">حاسبة معدل التوجيهي الوزارية (من 700)</h3>
                <p style="font-size: 0.88rem; color: #64748b;">احتساب فوري لأعلى مادة اختيارية ودليل القبول لجامعات فلسطين.</p>
            </div>
        </div>
        <a href="{{ route('tawjihi.calculator') }}" style="background: #0f172a; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 10px; font-size: 0.9rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <span>جرب الحاسبة الآن</span>
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

</div>
@endsection
