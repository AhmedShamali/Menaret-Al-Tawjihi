@extends('layouts.app')

@section('title', $stage->label_ar)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 10px 0 60px;">

    <!-- Back Navigation & Title -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('stages.index') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: #ffffff; border: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; font-size: 1.1rem; transition: background 0.2s;">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $stage->label_ar }}</h1>
                <p style="color: #64748b; font-size: 0.9rem; margin: 2px 0 0;">المباحث والمقررات التعليمية الرسمية المعتمدة</p>
            </div>
        </div>

        <a href="{{ route('tawjihi.calculator') }}" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; text-decoration: none; padding: 9px 18px; border-radius: 10px; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-calculator"></i>
            <span>حاسبة معدل التوجيهي</span>
        </a>
    </div>

    <!-- Subjects Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        @forelse($stage->subjects as $subject)
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 26px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04); transition: border-color 0.2s, box-shadow 0.2s;">
            <div>
                <!-- Top Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                    <div style="width: 54px; height: 54px; border-radius: 14px; background: #f8fafc; border: 1px solid #e2e8f0; display: grid; place-items: center; font-size: 1.8rem;">
                        {{ $subject->icon ?? '📘' }}
                    </div>

                    <!-- Grade Weight Tag -->
                    @if($subject->subject_key === 'math_12_sci')
                        <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.76rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                            من 200 علامة (النجاح: 100)
                        </span>
                    @elseif(in_array($subject->subject_key, ['arabic_12_lit', 'english_12_lit']))
                        <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.76rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                            من 150 علامة (النجاح: 75)
                        </span>
                    @else
                        <span style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 0.76rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                            من 100 علامة (النجاح: 50)
                        </span>
                    @endif
                </div>

                <!-- Subject Title -->
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
                    {{ $subject->name_ar }}
                </h3>

                <!-- Subject Description -->
                <p style="color: #64748b; font-size: 0.88rem; line-height: 1.6; margin-bottom: 20px;">
                    {{ $subject->description ?? 'شروحات المبحث وملخصات الوحدات والدروس وفق المنهاج الفلسطيني الوزاري.' }}
                </p>
            </div>

            <!-- Bottom Pricing & Action -->
            <div style="padding-top: 16px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.78rem; color: #94a3b8; display: block;">سعر الاشتراك</span>
                    <strong style="font-size: 1.15rem; color: #0f172a;">{{ number_format($subject->effective_price ?? 150, 0) }} ₪</strong>
                </div>

                <a href="{{ route('subject.show', $subject->id) }}" style="background: #1e40af; color: #ffffff; text-decoration: none; padding: 9px 18px; border-radius: 10px; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;">
                    <span>دخول المبحث</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 80px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;">
            <p style="color: #64748b; font-size: 1.1rem;">سيتم إضافة مقررات هذا الفرع قريباً...</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
