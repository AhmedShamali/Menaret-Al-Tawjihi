@extends('layouts.app')

@section('title', 'كتالوج وباقات المواد الدراسية | توجيهي فلسطين')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding-bottom: 90px; animation: fadeIn 0.5s ease;">

    <!-- الترويسة الترحيبية -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%); border-radius: 22px; padding: 35px 30px; color: white; margin-bottom: 30px; position: relative; overflow: hidden; box-shadow: 0 10px 30px -5px rgba(37, 99, 235, 0.3);">
        <div style="position: relative; z-index: 2; max-width: 650px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); padding: 5px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; margin-bottom: 14px; color: #93c5fd;">
                <i class="fa-solid fa-graduation-cap"></i> المنهاج الفلسطيني المعتمد للتوجيهي 🇵🇸
            </div>
            <h1 style="font-size: 1.85rem; font-weight: 900; margin-bottom: 10px; line-height: 1.3;">
                اختر مادتك الدراسية أو باقتك الوزارية الكاملة
            </h1>
            <p style="margin: 0; font-size: 0.95rem; color: #e0f2fe; line-height: 1.7;">
                اشترك في مادة واحدة أو حدد عدة مواد دراسية معاً لتحصل تلقائياً على <strong>خصم الباقة 15%</strong> عند اختيار 3 مواد أو أكثر!
            </p>
        </div>

        <div style="position: absolute; left: 30px; bottom: 20px; opacity: 0.15; font-size: 8rem; pointer-events: none;">
            <i class="fa-solid fa-shapes"></i>
        </div>
    </div>

    <!-- شريط تصفية الفروع -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 25px; background: white; padding: 14px 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.88rem; color: #334155;">
            <i class="fa-solid fa-layer-group" style="color: #0284c7;"></i> تصفية المواد حسب الفرع:
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('student.courses.catalog') }}" style="text-decoration: none; padding: 6px 14px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; {{ empty($stageId) ? 'background: #0284c7; color: white;' : 'background: #f1f5f9; color: #475569;' }}">
                جميع الفروع
            </a>
            @foreach($stages as $stg)
                <a href="{{ route('student.courses.catalog', ['stage_id' => $stg->id]) }}" style="text-decoration: none; padding: 6px 14px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; {{ $stageId == $stg->id ? 'background: #0284c7; color: white;' : 'background: #f1f5f9; color: #475569;' }}">
                    {{ $stg->name ?? $stg->name_ar }}
                </a>
            @endforeach
        </div>
    </div>

    <form id="enrollmentForm" onsubmit="handleCheckout(event)">
        @csrf

        <!-- شبكة بطاقات المواد -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @forelse($subjects as $sub)
                @php
                    $isEnrolled = in_array($sub->id, $enrolledSubjectIds);
                    $isPending = in_array($sub->id, $pendingSubjectIds ?? []);
                    $effectivePrice = $sub->effective_price;
                @endphp

                <div class="course-card {{ $isEnrolled ? 'enrolled' : ($isPending ? 'pending-card' : '') }}" style="background: white; border: 2px solid {{ $isEnrolled ? '#10b981' : ($isPending ? '#f59e0b' : '#e2e8f0') }}; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; position: relative; transition: all 0.25s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">

                    @if($isEnrolled)
                        <div style="position: absolute; top: 16px; left: 16px; background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-circle-check"></i> اشتراك معتمد ومفعّل ✔
                        </div>
                    @elseif($isPending)
                        <div style="position: absolute; top: 16px; left: 16px; background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px; display: flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);">
                            <i class="fa-solid fa-clock-rotate-left"></i> بانتظار موافقة المدير ⏳
                        </div>
                    @elseif($sub->is_free)
                        <div style="position: absolute; top: 16px; left: 16px; background: #e0f2fe; color: #0369a1; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;">
                            مجانية تجريبية 🎁
                        </div>
                    @elseif($sub->discount_price_ils > 0)
                        <div style="position: absolute; top: 16px; left: 16px; background: #fef3c7; color: #b45309; font-size: 0.75rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;">
                            وفر {{ round((($sub->price_ils - $sub->discount_price_ils) / $sub->price_ils) * 100) }}% 🔥
                        </div>
                    @endif

                    <div>
                        <!-- رأس البطاقة -->
                        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
                            <div style="width: 50px; height: 50px; border-radius: 14px; background: {{ $sub->color ?? '#0284c7' }}15; color: {{ $sub->color ?? '#0284c7' }}; display: grid; place-items: center; font-size: 1.4rem; flex-shrink: 0;">
                                <i class="fa-solid {{ $sub->icon ?? 'fa-book' }}"></i>
                            </div>
                            <div>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">
                                    {{ optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي' }}
                                </span>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 2px 0 0;">
                                    {{ $sub->name_ar }}
                                </h3>
                            </div>
                        </div>

                        <!-- الوصف -->
                        <p style="font-size: 0.83rem; color: #64748b; line-height: 1.6; margin-bottom: 16px; min-height: 40px;">
                            {{ $sub->description ?: 'شرح تفاعلي متكامل للمنهاج الفلسطيني مع حلول الأسئلة الوزارية ونماذج الإنجاز.' }}
                        </p>

                        <!-- إحصائيات المحتوى -->
                        <div style="display: flex; gap: 14px; margin-bottom: 20px; font-size: 0.78rem; color: #475569; font-weight: 600; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                            <span><i class="fa-solid fa-circle-play" style="color: #0284c7;"></i> {{ $sub->contents_count ?? 0 }} درس مصور</span>
                            <span><i class="fa-solid fa-file-pen" style="color: #10b981;"></i> {{ $sub->exams_count ?? 0 }} اختبار تفاعلي</span>
                        </div>
                    </div>

                    <!-- التسعير وزر الاختيار -->
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            @if($sub->is_free)
                                <span style="font-size: 1.25rem; font-weight: 900; color: #16a34a;">0 ₪</span>
                                <small style="display: block; font-size: 0.72rem; color: #94a3b8;">دخول مجاني بالكامل</small>
                            @else
                                <div style="display: flex; align-items: baseline; gap: 6px;">
                                    <span style="font-size: 1.35rem; font-weight: 900; color: #0f172a; font-family: monospace;">
                                        {{ number_format($effectivePrice, 0) }} ₪
                                    </span>
                                    @if($sub->discount_price_ils > 0)
                                        <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.85rem; font-family: monospace;">
                                            {{ number_format($sub->price_ils, 0) }} ₪
                                        </span>
                                    @endif
                                </div>
                                <small style="display: block; font-size: 0.72rem; color: #94a3b8;">اشتراك شامل حتى نهاية العام</small>
                            @endif
                        </div>

                        <div>
                            @if($isEnrolled)
                                <a href="{{ route('student.subjects.show', $sub->id) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 12px; background: #ecfdf5; color: #059669; text-decoration: none; font-weight: 700; font-size: 0.85rem;">
                                    <span>متابعة التعلم</span> <i class="fa-solid fa-arrow-left"></i>
                                </a>
                            @elseif($isPending)
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-weight: 700; font-size: 0.82rem;" title="طلب اشتراكك قيد التدقيق والموافقة من قِبل إدارة المنصة">
                                    <i class="fa-solid fa-hourglass-half"></i> <span>بانتظار موافقة المدير ⏳</span>
                                </span>
                            @else
                                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; background: #f8fafc; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; transition: 0.2s;" onmouseover="this.style.borderColor='#0284c7'" onmouseout="this.style.borderColor='#cbd5e1'">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" data-price="{{ $effectivePrice }}" data-name="{{ $sub->name_ar }}" onchange="updateCartBar()" style="width: 17px; height: 17px; accent-color: #0284c7; cursor: pointer;">
                                    <span>تحديد المادة</span>
                                </label>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div style="grid-column: 1 / -1; padding: 50px; text-align: center; background: white; border-radius: 20px; color: #94a3b8;">
                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 12px; opacity: 0.3;"></i>
                    <p style="font-size: 1rem; font-weight: 700;">لا توجد مواد مسجلة لهذا الفرع حالياً.</p>
                </div>
            @endforelse
        </div>

        <!-- شريط السلة العائم الثابت بالأسفل (Floating Cart Bar) -->
        <div id="floatingCartBar" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: calc(100% - 40px); max-width: 900px; background: #0f172a; border: 1px solid #334155; border-radius: 20px; padding: 16px 28px; color: white; z-index: 1000; box-shadow: 0 15px 35px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #0284c7; display: grid; place-items: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.95rem; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                            <span>المواد المحددة: <span id="cartCount" style="color: #38bdf8;">0</span> مواد</span>
                            <span id="bundleBadge" style="display: none; background: #10b981; color: white; font-size: 0.72rem; padding: 2px 8px; border-radius: 20px;">
                                خصم الباقة 15% مفعّل 🔥
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: #94a3b8;" id="cartSummaryText">حدد موادك للاشتراك المباشر</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="text-align: left;">
                        <span style="font-size: 0.72rem; color: #94a3b8; display: block;">المجموع الإجمالي</span>
                        <div style="font-size: 1.4rem; font-weight: 900; color: #38bdf8; font-family: monospace;">
                            <span id="cartTotal">0</span> ₪
                        </div>
                    </div>

                    <button type="submit" id="btnGoToCheckout" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; border: none; padding: 13px 26px; border-radius: 14px; font-weight: 800; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4); transition: 0.2s;">
                        <span>إتمام الدفع الفلسطيني</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
function updateCartBar() {
    const checked = document.querySelectorAll('input[name="subject_ids[]"]:checked');
    const bar = document.getElementById('floatingCartBar');
    const countEl = document.getElementById('cartCount');
    const totalEl = document.getElementById('cartTotal');
    const badgeEl = document.getElementById('bundleBadge');
    const summaryText = document.getElementById('cartSummaryText');

    let count = checked.length;
    let subtotal = 0;
    let names = [];

    checked.forEach(box => {
        subtotal += parseFloat(box.getAttribute('data-price') || 0);
        names.push(box.getAttribute('data-name'));
    });

    if (count > 0) {
        bar.style.display = 'block';
        countEl.textContent = count;

        let discount = 0;
        if (count >= 3 && subtotal > 0) {
            discount = subtotal * 0.15;
            badgeEl.style.display = 'inline-block';
        } else {
            badgeEl.style.display = 'none';
        }

        let finalTotal = Math.max(0, subtotal - discount);
        totalEl.textContent = Math.round(finalTotal);

        summaryText.textContent = names.slice(0, 3).join(' + ') + (names.length > 3 ? ' +' + (names.length - 3) + ' أخرى' : '');
    } else {
        bar.style.display = 'none';
    }
}

function handleCheckout(e) {
    e.preventDefault();
    const form = document.getElementById('enrollmentForm');
    const checked = document.querySelectorAll('input[name="subject_ids[]"]:checked');

    if (checked.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'يرجى اختيار مادة',
            text: 'حدد مادة دراسية واحدة على الأقل للاشتراك في المنهاج.'
        });
        return;
    }

    const btn = document.getElementById('btnGoToCheckout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التجهيز...';

    const formData = new FormData(form);

    axios.post('{{ route("student.courses.checkout") }}', formData)
        .then(res => {
            window.location.href = res.data.redirect || '{{ route("student.checkout.show") }}';
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>إتمام الدفع الفلسطيني</span> <i class="fa-solid fa-arrow-left"></i>';
            const msg = err.response?.data?.message || 'حدث خطأ أثناء تجهيز الطلب، يرجى المحاولة ثانية.';
            Swal.fire({ icon: 'error', title: 'تنبيه', text: msg });
        });
}
</script>
@endsection
