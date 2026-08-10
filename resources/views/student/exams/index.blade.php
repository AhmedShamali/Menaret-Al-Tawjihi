@extends('layouts.app')

@section('title', 'اختباراتي الدراسية')

@section('content')
<div style="direction: rtl; text-align: right; padding: 20px; font-family: 'Tajawal', sans-serif;">

    <!-- هيدر البوابة الأكاديمية -->
    <div style="background: #0f172a; color: #ffffff; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <span style="background: #3b82f6; color: #fff; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; margin-bottom: 10px;">بوابة الطالب الأكاديمية</span>
            <h2 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 800;">مرحباً بك، {{ optional($student)->name_ar ?? auth()->user()->name }}</h2>
            <p style="margin: 0; color: #94a3b8; font-size: 15px;">
                المرحلة الدراسية الحالية:
                <span style="color: #f59e0b; font-weight: bold;">
                    {{ $currentStageName ?? 'غير محددة' }}
                </span>
            </p>
        </div>
        <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 12px; text-align: center;">
            <span style="display: block; color: #94a3b8; font-size: 12px; margin-bottom: 4px;">التاريخ الأكاديمي</span>
            <strong style="color: #fff; font-family: monospace; font-size: 14px;">{{ date('Y/m/d') }}</strong>
        </div>
    </div>

    <!-- تنبيه إذا كان الحساب غير مرتبط بمرحلة -->
    @if(!$student || !$student->stage_id)
        <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; font-weight: bold;">
            ⚠️ تنبيه: حساب الطالب غير مرتبط بشكل صحيح بالمرحلة الدراسية في قاعدة البيانات. يرجى مراجعة الإدارة لربط السجل بـ stage_id.
        </div>
    @endif

    <!-- رسائل التنبيه والنجاح أو الخطأ -->
    @if(session('success'))
        <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
            ✕ {{ session('error') }}
        </div>
    @endif

    <!-- عنوان القسم -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h4 style="margin: 0; font-size: 18px; font-weight: bold; color: #1e293b;">
            📋 الاختبارات المتاحة لمرحلتك
        </h4>
        <span style="background: #f1f5f9; color: #475569; padding: 6px 15px; border-radius: 20px; font-size: 13px; border: 1px solid #cbd5e1;">
            عدد الاختبارات: {{ isset($exams) ? $exams->count() : 0 }}
        </span>
    </div>

    <!-- شبكة الاختبارات -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($exams as $exam)
            @php
                $hasSubmitted = $student && $exam->submissions && $exam->submissions->where('student_id', $student->id)->isNotEmpty();
                $submissionRecord = $hasSubmitted ? $exam->submissions->where('student_id', $student->id)->first() : null;
            @endphp
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                            {{ optional($exam->subject)->name_ar ?? (optional($exam->subject)->name ?? 'مادة عامة') }}
                        </span>
                        <span style="background: #2563eb; color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                            {{ optional($exam->stage)->name_ar ?? (optional($exam->stage)->name ?? 'عام') }}
                        </span>
                    </div>

                    <h5 style="margin: 0 0 10px 0; font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.4;">
                        {{ $exam->title }}
                    </h5>

                    <p style="margin: 0 0 20px 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                        {{ $exam->description ?? 'اختبار معتمد ضمن خطتك الدراسية لهذا الفصل، يرجى الالتزام بالوقت المخصص.' }}
                    </p>

                    <div style="background: #f8fafc; border-radius: 12px; padding: 12px; margin-bottom: 20px; border: 1px solid #f1f5f9; display: flex; text-align: center;">
                        <div style="flex: 1; border-left: 1px solid #e2e8f0;">
                            <span style="display: block; color: #94a3b8; font-size: 11px; margin-bottom: 2px;">مدة الاختبار</span>
                            <strong style="color: #1e293b; font-size: 13px;">{{ $exam->duration_minutes }} دقيقة</strong>
                        </div>
                        <div style="flex: 1;">
                            <span style="display: block; color: #94a3b8; font-size: 11px; margin-bottom: 2px;">عدد الأسئلة</span>
                            <strong style="color: #1e293b; font-size: 13px;">{{ $exam->questions_count ?? ($exam->questions ? $exam->questions->count() : 0) }} أسئلة</strong>
                        </div>
                    </div>
                </div>

                <div>
                    @if($hasSubmitted)
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <button disabled style="flex: 1; background: #dcfce7; color: #166534; text-align: center; padding: 12px; border-radius: 12px; font-weight: bold; border: 1px solid #bbf7d0; cursor: not-allowed; font-size: 14px;">
                                ✓ تم التقديم
                            </button>
                            @if($submissionRecord)
                                <a href="{{ route('student.exams.result', $submissionRecord->id) }}" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; text-align: center; padding: 12px 16px; border-radius: 12px; font-weight: bold; text-decoration: none; font-size: 14px; white-space: nowrap;">
                                    النتيجة
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- زر مع دالة تأكيد البدء -->
                        <button type="button" onclick="confirmStartExam('{{ route('student.exams.take', $exam->id) }}')" style="display: block; width: 100%; background: #2563eb; color: #fff; text-align: center; padding: 12px; border-radius: 12px; font-weight: bold; border: none; cursor: pointer; font-size: 14px; box-shadow: 0 4px 12px rgba(37,99,235,0.2);">
                            بدء الاختبار الآن ←
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 40px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 10px; color: #94a3b8;">📂</div>
                <h5 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #1e293b;">لا توجد اختبارات متاحة حالياً</h5>
                <p style="margin: 0; color: #64748b; font-size: 13px;">لم يتم طرح أي اختبارات جديدة لمرحلتك الدراسية في الوقت الحالي.</p>
            </div>
        @endforelse
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmStartExam(takeUrl) {
        Swal.fire({
            title: 'هل أنت متأكد من بدء الاختبار؟',
            text: 'تنبيه: يمكنك تقديم هذا الاختبار مرة واحدة فقط، وبمجرد البدء سيبدأ احتساب الوقت ولا يمكنك التراجع!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'نعم، ابدأ الاختبار',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = takeUrl;
            }
        });
    }
</script>
@endsection
