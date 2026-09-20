@extends('layouts.app')

@section('title', 'رصد درجات الطالب | ' . ($submission->student->name_ar ?? $submission->student->name ?? 'طالب'))

@section('content')
<div style="max-width: 1040px; margin: 0 auto; animation: fadeIn 0.5s ease; padding-bottom: 70px;">

    <!-- هيدر الصفحة الكلاسيكي -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <nav style="font-size: 0.8rem; color: #64748b; margin-bottom: 8px; font-weight: 700;">
                <a href="{{ route(auth()->user()->role . '.submissions.index') }}" style="color: #64748b; text-decoration: none;">سجل التسليمات</a>
                <span style="margin: 0 6px;">/</span>
                <span style="color: #1e40af;">رصد واعتماد الدرجات</span>
            </nav>
            <h1 style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin: 0;">
                مراجعة الحلول الأكاديمية 🖋️
            </h1>
            <p style="margin-top: 5px; color: #64748b; font-size: 0.92rem;">
                رصد درجات الطالب: <strong style="color: #1e40af;">{{ $submission->student->name_ar ?? $submission->student->name }}</strong>
                @if($submission->student && $submission->student->stage)
                    <span style="background: #f1f5f9; color: #334155; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; margin-inline-start: 8px;">
                        {{ $submission->student->stage->name_ar }}
                    </span>
                @endif
                • الاختبار: <strong>{{ $submission->exam->title }}</strong>
            </p>
        </div>
        <div style="text-align: center; background: #fff; padding: 12px 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 2px;">الدرجة الكلية للاختبار</div>
            <div style="font-size: 1.8rem; font-weight: 900; color: #1e40af; font-family: monospace;">
                <span id="header_current_score">{{ $submission->total_earned_grade ?? 0 }}</span>
                <span style="font-size: 1rem; color: #94a3b8; font-weight: 600;">/ {{ $submission->exam->total_grade ?? $submission->exam->questions->sum('points') }}</span>
            </div>
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
                    <span style="font-size: 1.6rem;">⚠️</span>
                    <div>
                        <h3 style="margin: 0; color: #991b1b; font-size: 1.1rem; font-weight: 800;">تقرير النزاهة الأكاديمية: تم رصد أنشطة اشتباه غش</h3>
                        <p style="margin: 3px 0 0; color: #b91c1c; font-size: 0.84rem;">قام النظام برصد تصرفات غير مصرح بها أثناء الجلسة. يمكنك كمعلم تقييم الإجابات وتطبيق خصم درجات موثق مع بيان السبب للطالب.</p>
                    </div>
                </div>
                <span style="background: #dc2626; color: white; padding: 5px 14px; border-radius: 20px; font-weight: 800; font-size: 0.78rem;">
                    تنبيه اشتباه نشط
                </span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; background: white; padding: 15px; border-radius: 12px; border: 1px solid #fee2e2;">
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">مرات مغادرة نافذة الامتحان</span>
                    <strong style="font-size: 1.5rem; color: #dc2626; font-family: monospace;">{{ $submission->tab_switches_count ?? 0 }}</strong>
                </div>
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">محاولات لقطات الشاشة أو الطباعة</span>
                    <strong style="font-size: 1.5rem; color: #dc2626; font-family: monospace;">{{ $submission->screenshots_count ?? 0 }}</strong>
                </div>
                <div style="text-align: center;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700;">حالة ظهور النتيجة للطالب</span>
                    <strong style="font-size: 0.95rem; color: {{ $submission->is_published ? '#059669' : '#d97706' }}; font-weight: 800; display: block; margin-top: 4px;">
                        {{ $submission->is_published ? 'معلنة للطالب 🟢' : 'محجوبة حتى الاعتماد 🔒' }}
                    </strong>
                </div>
            </div>
            @if(!empty($submission->cheating_flags) && is_array($submission->cheating_flags))
                <details style="margin-top: 12px; cursor: pointer;">
                    <summary style="font-size: 0.82rem; color: #991b1b; font-weight: 700;">عرض السجل الزمني للتنبيهات الموثقة ({{ count($submission->cheating_flags) }}) 📋</summary>
                    <ul style="margin: 8px 0 0; padding-inline-start: 20px; font-size: 0.8rem; color: #7f1d1d; line-height: 1.8;">
                        @foreach($submission->cheating_flags as $flag)
                            <li>
                                <strong>{{ ($flag['type'] ?? '') == 'tab_switch' ? 'مغادرة النافذة / التطبيق' : 'محاولة لقطة شاشة / طباعة' }}</strong>
                                — {{ $flag['details'] ?? 'حركة مريبة' }}
                                @if(!empty($flag['time']))
                                    <span style="color: #991b1b; font-size: 0.75rem;">({{ $flag['time'] }})</span>
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
                    <h3 style="margin: 0; color: #166534; font-size: 1.05rem; font-weight: 800;">سجل النزاهة الأكاديمية: جلسة موثوقة ونزيهة 100%</h3>
                    <p style="margin: 2px 0 0; color: #15803d; font-size: 0.82rem;">لم يسجل النظام أي محاولات لمغادرة الصفحة أو تصوير الشاشة أثناء أداء الاختبار.</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 0.8rem; color: #475569; font-weight: 700;">ظهور النتيجة:</span>
                <span style="background: {{ $submission->is_published ? '#dcfce7' : '#fef3c7' }}; color: {{ $submission->is_published ? '#15803d' : '#b45309' }}; font-weight: 800; padding: 4px 10px; border-radius: 8px; font-size: 0.8rem;">
                    {{ $submission->is_published ? 'معلنة للطالب 🟢' : 'محجوبة حتى الاعتماد 🔒' }}
                </span>
            </div>
        </div>
    @endif

    <form id="gradingForm">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 25px;">
            
            {{-- إذا كانت هناك إجابات مسجلة مسبقاً --}}
            @if($submission->answers && $submission->answers->isNotEmpty())
                @foreach($submission->answers as $index => $ans)
                <div class="glass-card" style="padding: 30px; border-right: 6px solid {{ $ans->question->type == 'mcq' ? '#10b981' : '#3b82f6' }}; border-radius: 18px; background: white; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <span style="font-weight: 800; color: #64748b; font-size: 0.9rem;">
                            سؤال #{{ $index + 1 }} ({{ $ans->question->type == 'mcq' ? 'اختيار من متعدد' : 'سؤال مقالي / كتابي' }})
                        </span>
                        <span style="background: #f1f5f9; font-weight: 800; color: #1e293b; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">
                            الدرجة المقررة: {{ $ans->question->points }} علامة
                        </span>
                    </div>

                    <h3 style="font-size: 1.18rem; color: #0f172a; margin-bottom: 18px; line-height: 1.6; font-weight: 700;">
                        {{ $ans->question->question_text }}
                    </h3>

                    @if($ans->question->image_url)
                        <div style="margin: 15px 0 20px; text-align: center; background: #f8fafc; padding: 15px; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <img src="{{ $ans->question->image_url }}" 
                                 alt="صورة السؤال" 
                                 onclick="openZoomModal(this.src)"
                                 style="max-height: 240px; max-width: 100%; object-fit: contain; border-radius: 8px; cursor: zoom-in;">
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                                <i class="fa-solid fa-magnifying-glass-plus"></i> انقر لتكبير صورة السؤال
                            </div>
                        </div>
                    @endif

                    <div style="background: #f8fafc; padding: 20px; border-radius: 14px; border: 1px solid #e2e8f0;">
                        @if($ans->question->type == 'mcq')
                            @php
                                $isCorrect = strtolower(trim((string)$ans->answer_text)) === strtolower(trim((string)$ans->question->correct_answer));
                            @endphp
                            <div style="font-weight: 700; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    إجابة الطالب: 
                                    <span style="color: {{ $isCorrect ? '#059669' : '#dc2626' }}; font-weight: 800;">
                                        ({{ strtoupper($ans->answer_text ?? 'لا توجد إجابة') }}) {{ $ans->question->{$ans->answer_text} ?? '' }}
                                    </span>
                                    {!! $isCorrect ? ' <span style="margin-inline-start: 8px;">✅ (صحيحة)</span>' : ' <span style="margin-inline-start: 8px;">❌ (خاطئة)</span>' !!}
                                </div>
                                <div style="font-size: 0.85rem; color: #64748b;">
                                    الإجابة النموذجية: <b style="color: #059669;">({{ strtoupper($ans->question->correct_answer) }}) {{ $ans->question->{$ans->question->correct_answer} ?? '' }}</b>
                                </div>
                            </div>
                            <div style="margin-top: 12px; display: flex; align-items: center; gap: 10px;">
                                <label style="font-weight: 700; font-size: 0.9rem; color: #334155;">الدرجة المرصودة:</label>
                                <input type="number" name="grades[{{ $ans->id }}]" 
                                       value="{{ $ans->points_awarded ?? ($isCorrect ? $ans->question->points : 0) }}" 
                                       max="{{ $ans->question->points }}" min="0" step="0.5"
                                       class="f-input question-score-input" 
                                       oninput="recalcLiveScore()"
                                       style="width: 100px; text-align: center; font-weight: 800; font-size: 1.1rem; color: #1e40af;">
                                <span style="color: #94a3b8; font-size: 0.85rem;">من {{ $ans->question->points }}</span>
                            </div>
                        @else
                            <div style="margin-bottom: 15px;">
                                <strong style="display: block; margin-bottom: 8px; color: #64748b; font-size: 0.88rem;">نص إجابة الطالب:</strong>
                                <div style="line-height: 1.8; font-size: 1rem; color: #0f172a; background: white; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; white-space: pre-wrap;">{{ $ans->answer_text ?? 'لم يتم تدوين نص إجابة من قبل الطالب' }}</div>

                                @if($ans->file_path)
                                    <a href="{{ asset('storage/'.$ans->file_path) }}" target="_blank" style="margin-top: 12px; background: white; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 0.88rem;">
                                        <span>📂</span> فتح ملف الحل المرفق للطالب (PDF/صورة)
                                    </a>
                                @endif
                            </div>

                            <div style="display: flex; align-items: center; gap: 12px; padding-top: 15px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                                <label style="font-weight: 800; color: #0f172a;">رصد الدرجة المستحقة للسؤال المقالي:</label>
                                <input type="number" name="grades[{{ $ans->id }}]"
                                       value="{{ $ans->points_awarded ?? 0 }}"
                                       max="{{ $ans->question->points }}"
                                       min="0"
                                       step="0.5"
                                       class="f-input question-score-input"
                                       oninput="recalcLiveScore()"
                                       style="width: 110px; text-align: center; font-weight: 900; font-size: 1.3rem; color: #2563eb; border-color: #3b82f6;">
                                <span style="color: #64748b; font-weight: 600;">من أصل {{ $ans->question->points }} درجة</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                {{-- إذا كان التسليم خالياً من إجابات مسجلة (مثل حالة التسجيل أثناء الرصد أو التسليم المبكر) --}}
                <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 14px; padding: 16px 20px; color: #92400e; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px; font-weight: 800; margin-bottom: 4px;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>تنبيه: تم إنشاء هذا التسليم أثناء جلسة الرصد الأكاديمي، ولم يتم تدوين إجابات رقمية مباشرة.</span>
                    </div>
                    <p style="margin: 0; font-size: 0.85rem;">يمكنك كمعلم استعراض أسئلة الاختبار أدناه ورصد الدرجة المستحقة أو اعتماد درجة يدوية مباشرة مع توثيق سبب الخصم إن وجد.</p>
                </div>

                @foreach($submission->exam->questions as $index => $q)
                <div class="glass-card" style="padding: 25px; border-radius: 16px; background: white; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-weight: 800; color: #64748b;">سؤال #{{ $index + 1 }} ({{ $q->type == 'mcq' ? 'موضوعي' : 'مقالي' }})</span>
                        <span style="font-weight: 700; color: #1e293b;">{{ $q->points }} درجات</span>
                    </div>
                    <h3 style="font-size: 1.1rem; color: #0f172a; margin: 0 0 12px;">{{ $q->question_text }}</h3>
                    @if($q->image_url)
                        <img src="{{ $q->image_url }}" alt="صورة السؤال" onclick="openZoomModal(this.src)" style="max-height: 180px; max-width: 100%; border-radius: 8px; margin-bottom: 10px; cursor: zoom-in;">
                    @endif
                    <div style="background: #f8fafc; padding: 12px; border-radius: 10px; font-size: 0.88rem; color: #64748b;">
                        لم تُسجل إجابة لهذا السؤال أثناء الجلسة.
                    </div>
                </div>
                @endforeach
            @endif

            {{-- بطاقة الخصم الأكاديمي وملاحظات المعلم (Deduction & Feedback Card) --}}
            <div style="background: white; border: 2px solid #e2e8f0; border-radius: 20px; padding: 30px; box-shadow: 0 6px 20px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: #fef2f2; color: #dc2626; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-scale-unbalanced"></i>
                        </span>
                        <div>
                            <h3 style="margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 800;">سياسة الخصم الأكاديمي والملاحظات التوجيهية</h3>
                            <p style="margin: 2px 0 0; color: #64748b; font-size: 0.82rem;">يمكنك خصم علامات محددة وتوثيق السبب ليظهر بوضوح في تقرير نتيجة الطالب.</p>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 800; color: #1e293b; margin-bottom: 8px; font-size: 0.92rem;">
                            <i class="fa-solid fa-minus-circle text-danger"></i> مقدار خصم العلامات (درجات مخصومة):
                        </label>
                        <input type="number" 
                               name="deduction_amount" 
                               id="deductionAmountInput" 
                               value="{{ $submission->deduction_amount ?? 0 }}" 
                               min="0" 
                               max="{{ $submission->exam->total_grade ?? 100 }}" 
                               step="0.5" 
                               class="f-input" 
                               oninput="recalcLiveScore()"
                               style="width: 100%; font-size: 1.2rem; font-weight: 800; color: #dc2626; text-align: center; border-color: #fecaca; background: #fffcfc;"
                               placeholder="0">
                        <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;">
                            <button type="button" onclick="setDeductionQuick(0)" class="quick-chip">بدون خصم (0)</button>
                            <button type="button" onclick="setDeductionQuick(2)" class="quick-chip">-2 علامات</button>
                            <button type="button" onclick="setDeductionQuick(5)" class="quick-chip">-5 علامات</button>
                            <button type="button" onclick="setDeductionQuick(10)" class="quick-chip">-10 علامات</button>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 800; color: #1e293b; margin-bottom: 8px; font-size: 0.92rem;">
                            <i class="fa-solid fa-circle-question text-primary"></i> سبب خصم العلامة (يظهر للطالب):
                        </label>
                        <textarea name="deduction_reason" 
                                  id="deductionReasonInput" 
                                  rows="2" 
                                  class="f-input" 
                                  style="width: 100%; resize: vertical;"
                                  placeholder="اكتب سبب الخصم بوضوح (مثلاً: خصم 5 علامات بسبب تكرار مغادرة نافذة الاختبار 3 مرات أثناء جلسة الحل)">{{ $submission->deduction_reason }}</textarea>
                        <div style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;">
                            <button type="button" onclick="setReasonQuick('خصم درجات بسبب تكرار مغادرة نافذة الاختبار أثناء جلسة الحل')" class="quick-chip-text">مغادرة النافذة</button>
                            <button type="button" onclick="setReasonQuick('خصم درجات بسبب رصد محاولات تصوير الشاشة أثناء الامتحان')" class="quick-chip-text">لقطات شاشة</button>
                            <button type="button" onclick="setReasonQuick('خصم درجات لعدم الالتزام بتعليمات الإجابة النموذجية')" class="quick-chip-text">عدم التزام بالتعليمات</button>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-weight: 800; color: #1e293b; margin-bottom: 8px; font-size: 0.92rem;">
                        <i class="fa-solid fa-comment-dots text-primary"></i> ملاحظات وتوجيهات المعلم الأكاديمية للطالب:
                    </label>
                    <textarea name="teacher_notes" 
                              rows="2" 
                              class="f-input" 
                              style="width: 100%; resize: vertical;"
                              placeholder="أضف نصيحة أو توجيهاً للطالب لتطوير مستواه الأكاديمي والتحذير من المخالفات مستقبلاً...">{{ $submission->teacher_notes }}</textarea>
                </div>

                {{-- حاسبة النتيجة التفاعلية المباشرة --}}
                <div style="margin-top: 25px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 24px; display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; gap: 15px;">
                    <div style="text-align: center;">
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; display: block;">مجموع درجات الأسئلة</span>
                        <strong style="font-size: 1.4rem; color: #0f172a; font-family: monospace;" id="live_subtotal_score">
                            {{ $submission->answers->sum('points_awarded') }}
                        </strong>
                    </div>
                    <div style="font-size: 1.4rem; color: #dc2626; font-weight: 900;">-</div>
                    <div style="text-align: center;">
                        <span style="font-size: 0.8rem; color: #dc2626; font-weight: 700; display: block;">الخصم المطبق</span>
                        <strong style="font-size: 1.4rem; color: #dc2626; font-family: monospace;" id="live_deduction_score">
                            {{ $submission->deduction_amount ?? 0 }}
                        </strong>
                    </div>
                    <div style="font-size: 1.4rem; color: #059669; font-weight: 900;">=</div>
                    <div style="text-align: center;">
                        <span style="font-size: 0.85rem; color: #059669; font-weight: 800; display: block;">صافي الدرجة النهائية المستحقة</span>
                        <strong style="font-size: 1.8rem; color: #059669; font-family: monospace;" id="live_final_score">
                            {{ $submission->total_earned_grade ?? 0 }}
                        </strong>
                    </div>
                </div>

                {{-- خيار تعديل يدوي مباشر للدرجة الأولية إن رغب المعلم --}}
                <div style="margin-top: 15px; text-align: left;">
                    <details style="font-size: 0.82rem; color: #64748b; cursor: pointer;">
                        <summary style="font-weight: 700;">تعديل الدرجة الأولية يدوياً بدون تفصيل الأسئلة ⚙️</summary>
                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
                            <span>الدرجة الأولية المباشرة (قبل الخصم):</span>
                            <input type="number" 
                                   name="override_total_grade" 
                                   id="overrideScoreInput" 
                                   step="0.5" 
                                   class="f-input" 
                                   style="width: 110px; padding: 6px 10px; text-align: center; font-weight: 700;" 
                                   oninput="recalcLiveScore()">
                        </div>
                    </details>
                </div>
            </div>

        </div>

        <div style="margin-top: 40px; text-align: center;">
            <button type="button" onclick="submitGradesToDB()" id="submitBtn" class="btn-primary-classic" style="padding: 18px 70px; font-size: 1.2rem; border-radius: 35px; background: #059669; color: white; border: none; cursor: pointer; font-weight: 800; box-shadow: 0 15px 35px rgba(5, 150, 105, 0.25); display: inline-flex; align-items: center; gap: 12px; transition: all 0.3s;">
                <i class="fa-solid fa-circle-check"></i>
                <span id="submitBtnText">اعتماد الدرجات وإعلان النتيجة للطالب ✅</span>
            </button>
            <p style="margin-top: 10px; font-size: 0.85rem; color: #64748b;">
                سيتم تحديث حالة التسليم إلى «تم التصحيح» ونشر الدرجة وسبب الخصم للطالب فوراً.
            </p>
        </div>
    </form>
</div>

<!-- Image Zoom Modal -->
<div id="imageZoomModal" onclick="closeZoomModal()" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; cursor: zoom-out;">
    <div style="position: relative; max-width: 90vw; max-height: 90vh;" onclick="event.stopPropagation()">
        <img id="zoomedImage" src="" alt="صورة مكبرة" style="max-width: 100%; max-height: 85vh; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
        <button type="button" onclick="closeZoomModal()" style="position: absolute; top: -15px; right: -15px; background: #ef4444; color: white; border: 2px solid white; border-radius: 50%; width: 36px; height: 36px; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            ×
        </button>
    </div>
</div>

<style>
    .f-input { padding: 12px 14px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-family: inherit; outline: none; transition: 0.25s; box-sizing: border-box; }
    .f-input:focus { border-color: #1e40af; box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.12); }
    .quick-chip { background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 4px 10px; border-radius: 14px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .quick-chip:hover { background: #e2e8f0; color: #0f172a; }
    .quick-chip-text { background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 3px 8px; border-radius: 8px; font-size: 0.73rem; cursor: pointer; }
    .quick-chip-text:hover { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
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

    function setDeductionQuick(val) {
        document.getElementById('deductionAmountInput').value = val;
        recalcLiveScore();
    }

    function setReasonQuick(text) {
        document.getElementById('deductionReasonInput').value = text;
    }

    function recalcLiveScore() {
        let subtotal = 0;
        const overrideInput = document.getElementById('overrideScoreInput');
        
        if (overrideInput && overrideInput.value.trim() !== '') {
            subtotal = parseFloat(overrideInput.value) || 0;
        } else {
            const inputs = document.querySelectorAll('.question-score-input');
            inputs.forEach(inp => {
                subtotal += parseFloat(inp.value) || 0;
            });
            // إذا لم توجد أسئلة أو مدخلات
            if (inputs.length === 0) {
                subtotal = parseFloat("{{ $submission->total_earned_grade ?? 0 }}") || 0;
            }
        }

        const deduction = Math.max(0, parseFloat(document.getElementById('deductionAmountInput').value) || 0);
        const finalScore = Math.max(0, subtotal - deduction);

        document.getElementById('live_subtotal_score').textContent = subtotal;
        document.getElementById('live_deduction_score').textContent = deduction;
        document.getElementById('live_final_score').textContent = finalScore;
        document.getElementById('header_current_score').textContent = finalScore;
    }

    function submitGradesToDB() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('submitBtnText');
        const form = document.getElementById('gradingForm');
        const formData = new FormData(form);

        btn.disabled = true;
        btnText.textContent = 'جاري الاعتماد وحفظ الدرجات...';

        const saveRouteUrl = "{{ route(auth()->user()->role . '.submissions.saveGrade', $submission->id) }}";
        const redirectIndexUrl = "{{ route(auth()->user()->role . '.submissions.index') }}";

        axios.post(saveRouteUrl, formData)
        .then(res => {
            if(res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: res.data.title || 'تم اعتماد الدرجات وإعلان النتيجة بنجاح 🎉',
                    text: `صافي الدرجة المرصودة: ${res.data.final_grade ?? ''}`,
                    showConfirmButton: false,
                    timer: 2200
                }).then(() => {
                    window.location.href = redirectIndexUrl;
                });
            } else {
                Swal.fire('تنبيه!', res.data.message || res.data.error || 'حدث خطأ غير متوقع', 'warning');
                btn.disabled = false;
                btnText.textContent = 'اعتماد الدرجات وإعلان النتيجة للطالب ✅';
            }
        })
        .catch(err => {
            console.error(err);
            const msg = (err.response && err.response.data) 
                ? (err.response.data.message || err.response.data.error || 'حدثت مشكلة أثناء الحفظ')
                : 'حدثت مشكلة في الاتصال، يرجى المحاولة ثانية';
            Swal.fire('خطأ!', msg, 'error');
            btn.disabled = false;
            btnText.textContent = 'اعتماد الدرجات وإعلان النتيجة للطالب ✅';
        });
    }

    // تشغيل الحساب الحي فور فتح الصفحة
    document.addEventListener('DOMContentLoaded', () => {
        recalcLiveScore();
    });
</script>
@endsection
