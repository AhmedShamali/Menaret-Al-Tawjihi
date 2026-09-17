@extends('layouts.app')

@section('title', __('كتالوج وباقات المواد الدراسية') . ' | ' . __('منارة التوجيهي'))

@section('content')
<div class="ed-catalog-page">

    <!-- الترويسة الأكاديمية الكلاسيكية -->
    <header class="ed-page-header">
        <div class="header-main-info">
            <div class="ed-flag-badge">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ __('المنهاج الفلسطيني المعتمد للتوجيهي 🇵🇸') }}</span>
            </div>
            <h1 class="ed-page-title">{{ __('اختر مادتك الدراسية أو باقتك الوزارية الكاملة') }}</h1>
            <p class="ed-page-desc">
                {{ __('اشترك في مادة واحدة أو حدد عدة مواد دراسية معاً لتحصل تلقائياً على خصم الباقة 15% عند اختيار 3 مواد أو أكثر!') }}
            </p>
        </div>
    </header>

    <!-- شريط تنبيه الخصم الخاص للطالب إن وجد -->
    @if(isset($student) && $student && $student->hasDiscount())
        <div class="ed-discount-banner">
            <div class="discount-icon-box">🏷️</div>
            <div class="discount-info">
                <h3>{{ __('مرحباً') }} {{ $student->name_ar }}! {{ $student->discount_label }}</h3>
                <p>
                    {{ $student->discount_notes ? $student->discount_notes . ' • ' : '' }}
                    {{ __('سيتم تطبيق هذا الخصم لصالحك تلقائياً عند اختيار المواد والانتقال لبوابة الاشتراك!') }}
                </p>
            </div>
            <span class="discount-pill">{{ __('خصم ساري ومفعّل ✔') }}</span>
        </div>
    @endif

    <!-- شريط تصفية الفروع الكلاسيكي -->
    <div class="ed-stage-filter-bar">
        <div class="filter-label">
            <i class="fa-solid fa-layer-group"></i>
            <span>{{ __('تصفية المواد حسب الفرع:') }}</span>
        </div>
        <div class="filter-pills-list">
            <a href="{{ route('student.courses.catalog') }}" class="filter-pill {{ empty($stageId) ? 'active' : '' }}">
                {{ __('جميع الفروع') }}
            </a>
            @foreach($stages as $stg)
                <a href="{{ route('student.courses.catalog', ['stage_id' => $stg->id]) }}" class="filter-pill {{ $stageId == $stg->id ? 'active' : '' }}">
                    {{ $stg->label_ar ?? ($stg->name_ar ?? $stg->name) }}
                </a>
            @endforeach
        </div>
    </div>

    <form id="enrollmentForm" onsubmit="handleCheckout(event)">
        @csrf

        <!-- شبكة بطاقات المواد الأكاديمية -->
        <div class="ed-courses-grid">
            @forelse($subjects as $sub)
                @php
                    $isEnrolled = in_array($sub->id, $enrolledSubjectIds);
                    $isPending = in_array($sub->id, $pendingSubjectIds ?? []);
                    $effectivePrice = $sub->effective_price;
                @endphp

                <div class="ed-course-card {{ $isEnrolled ? 'is-enrolled' : ($isPending ? 'is-pending' : '') }}">

                    @if($isEnrolled)
                        <div class="card-status-badge badge-enrolled">
                            <i class="fa-solid fa-circle-check"></i> {{ __('اشتراك معتمد ومفعّل') }}
                        </div>
                    @elseif($isPending)
                        <div class="card-status-badge badge-pending">
                            <i class="fa-solid fa-clock-rotate-left"></i> {{ __('بانتظار موافقة المدير') }} ⏳
                        </div>
                    @elseif($sub->is_free)
                        <div class="card-status-badge badge-free">
                            {{ __('مجانية تجريبية') }} 🎁
                        </div>
                    @elseif($sub->discount_price_ils > 0)
                        <div class="card-status-badge badge-discount">
                            {{ round((($sub->price_ils - $sub->discount_price_ils) / $sub->price_ils) * 100) }}% {{ __('خصم') }}
                        </div>
                    @endif

                    <div class="course-card-top">
                        <div class="course-icon-row">
                            <div class="course-icon-sq" style="color: {{ $sub->color ?? '#1e3a8a' }}; background: {{ $sub->color ?? '#1e3a8a' }}15;">
                                <i class="fa-solid {{ $sub->icon ?? 'fa-book' }}"></i>
                            </div>
                            <div>
                                <span class="course-stage-name">
                                    {{ optional($sub->stage)->label_ar ?? (optional($sub->stage)->name_ar ?? __('توجيهي')) }}
                                </span>
                                <h3 class="course-title">{{ $sub->name_ar ?? $sub->name }}</h3>
                            </div>
                        </div>

                        <p class="course-desc">
                            {{ $sub->description ?: __('شرح تفاعلي متكامل للمنهاج الفلسطيني مع حلول الأسئلة الوزارية ونماذج الإنجاز.') }}
                        </p>

                        <div class="course-stats-line">
                            <span><i class="fa-solid fa-circle-play"></i> {{ $sub->contents_count ?? 0 }} {{ __('دروس') }}</span>
                            <span><i class="fa-solid fa-file-pen"></i> {{ $sub->exams_count ?? 0 }} {{ __('اختبارات') }}</span>
                        </div>
                    </div>

                    <div class="course-card-bottom">
                        <div class="pricing-block">
                            @if($sub->is_free)
                                <span class="price-val free">0 ₪</span>
                                <small class="price-note">{{ __('دخول مجاني بالكامل') }}</small>
                            @else
                                <div class="price-row">
                                    <span class="price-val font-mono">{{ number_format($effectivePrice, 0) }} ₪</span>
                                    @if($sub->discount_price_ils > 0)
                                        <span class="price-original font-mono">{{ number_format($sub->price_ils, 0) }} ₪</span>
                                    @endif
                                </div>
                                <small class="price-note">{{ __('اشتراك شامل حتى نهاية العام') }}</small>
                            @endif
                        </div>

                        <div class="action-block">
                            @if($isEnrolled)
                                <a href="{{ route('student.subjects.show', $sub->id) }}" class="btn-goto-course">
                                    <span>{{ __('متابعة التعلم') }}</span>
                                    <i class="fa-solid fa-arrow-left arrow-icon"></i>
                                </a>
                            @elseif($isPending)
                                <span class="btn-pending-wait">
                                    <i class="fa-solid fa-hourglass-half"></i> {{ __('قيد المراجعة') }}
                                </span>
                            @else
                                <label class="select-box-label">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" data-price="{{ $effectivePrice }}" data-name="{{ $sub->name_ar ?? $sub->name }}" onchange="updateCartBar()" class="course-checkbox">
                                    <span>{{ __('تحديد المادة') }}</span>
                                </label>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="ed-empty-courses">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>{{ __('لا توجد مواد مسجلة لهذا الفرع حالياً.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- شريط السلة العائم الثابت بالأسفل (Floating Cart Bar) -->
        <div id="floatingCartBar" class="floating-cart-bar">
            <div class="cart-bar-content">
                <div class="cart-info-side">
                    <div class="cart-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                    <div>
                        <div class="cart-count-row">
                            <span>{{ __('المواد المحددة:') }} <strong id="cartCount" class="font-mono text-cyan">0</strong></span>
                            <span id="bundleBadge" class="bundle-discount-badge" style="display: none;">
                                {{ __('خصم الباقة 15% مفعّل') }} 🔥
                            </span>
                        </div>
                        <div class="cart-summary" id="cartSummaryText">{{ __('حدد موادك للاشتراك المباشر') }}</div>
                    </div>
                </div>

                <div class="cart-action-side">
                    <div class="cart-total-block">
                        <span class="total-label">{{ __('المجموع الإجمالي') }}</span>
                        <div class="total-number font-mono">
                            <span id="cartTotal">0</span> ₪
                        </div>
                    </div>

                    <button type="submit" id="btnGoToCheckout" class="btn-checkout">
                        <span>{{ __('إتمام الدفع الفلسطيني') }}</span>
                        <i class="fa-solid fa-arrow-left arrow-icon"></i>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<style>
/* ==========================================================
   CLASSIC ACADEMIC COURSE CATALOG STYLES (100% RESPONSIVE)
   ========================================================== */
.ed-catalog-page {
    width: 100%;
    margin: 0;
    padding: 0 0 90px;
    box-sizing: border-box;
}

.ed-page-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: 4px solid #1e3a8a;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.ed-flag-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #eff6ff;
    color: #1e3a8a;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.ed-page-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-page-desc {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    max-width: 650px;
    line-height: 1.5;
}

/* Discount Banner */
.ed-discount-banner {
    background: #faf5ff;
    border: 1px solid #d8b4fe;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.discount-icon-box {
    font-size: 1.4rem;
}

.discount-info {
    flex: 1;
    min-width: 240px;
}

.discount-info h3 {
    margin: 0 0 4px;
    font-size: 0.95rem;
    font-weight: 800;
    color: #581c87;
}

.discount-info p {
    margin: 0;
    font-size: 0.8rem;
    color: #7e22ce;
}

.discount-pill {
    background: #7c3aed;
    color: #ffffff;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.76rem;
    font-weight: 700;
}

/* Filter Bar */
.ed-stage-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
    background: #ffffff;
    padding: 12px 18px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 0.85rem;
    color: #334155;
}

.filter-label i { color: #1e3a8a; }

.filter-pills-list {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-pill {
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    transition: 0.15s;
}

.filter-pill.active {
    background: #1e3a8a;
    color: #ffffff;
}

.filter-pill:hover:not(.active) {
    background: #e2e8f0;
}

/* Courses Grid */
.ed-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.ed-course-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ed-course-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

.ed-course-card.is-enrolled { border-color: #86efac; }
.ed-course-card.is-pending { border-color: #fde68a; }

.card-status-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

html[dir="ltr"] .card-status-badge {
    left: auto;
    right: 14px;
}

.badge-enrolled { background: #dcfce7; color: #15803d; }
.badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-free { background: #eff6ff; color: #1e3a8a; }
.badge-discount { background: #fef3c7; color: #b45309; }

.course-icon-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.course-icon-sq {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.course-stage-name {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 2px 0 0;
}

.course-desc {
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1.55;
    margin: 0 0 16px;
    min-height: 38px;
}

.course-stats-line {
    display: flex;
    gap: 14px;
    font-size: 0.76rem;
    color: #475569;
    font-weight: 600;
    border-top: 1px solid #f1f5f9;
    padding-top: 10px;
    margin-bottom: 16px;
}

.course-stats-line i { margin-inline-end: 4px; color: #1e3a8a; }

.course-card-bottom {
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price-val {
    font-size: 1.25rem;
    font-weight: 900;
    color: #0f172a;
}

.price-val.free { color: #16a34a; }

.price-original {
    text-decoration: line-through;
    color: #94a3b8;
    font-size: 0.82rem;
}

.price-note {
    display: block;
    font-size: 0.7rem;
    color: #94a3b8;
}

.btn-goto-course {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    background: #eff6ff;
    color: #1e3a8a;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.82rem;
}

.btn-pending-wait {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
    font-weight: 700;
    font-size: 0.8rem;
}

.select-box-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.82rem;
    color: #334155;
    transition: 0.15s;
}

.select-box-label:hover {
    border-color: #1e3a8a;
    background: #ffffff;
}

.course-checkbox {
    width: 16px;
    height: 16px;
    accent-color: #1e3a8a;
    cursor: pointer;
}

.ed-empty-courses {
    grid-column: 1 / -1;
    padding: 50px 20px;
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    color: #94a3b8;
}

.ed-empty-courses i { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4; }

/* Floating Cart Bar */
.floating-cart-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 40px);
    max-width: 900px;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 14px;
    padding: 14px 24px;
    color: #ffffff;
    z-index: 1000;
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
}

.cart-bar-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
}

.cart-info-side {
    display: flex;
    align-items: center;
    gap: 14px;
}

.cart-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
}

.cart-count-row {
    font-size: 0.9rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
}

.text-cyan { color: #38bdf8; }

.bundle-discount-badge {
    background: #16a34a;
    color: #ffffff;
    font-size: 0.72rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 700;
}

.cart-summary {
    font-size: 0.78rem;
    color: #94a3b8;
    margin-top: 2px;
}

.cart-action-side {
    display: flex;
    align-items: center;
    gap: 18px;
}

.cart-total-block {
    text-align: right;
}

html[dir="ltr"] .cart-total-block {
    text-align: left;
}

.total-label {
    font-size: 0.7rem;
    color: #94a3b8;
    display: block;
}

.total-number {
    font-size: 1.3rem;
    font-weight: 900;
    color: #38bdf8;
    line-height: 1.1;
}

.btn-checkout {
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.15s;
}

.btn-checkout:hover {
    background: #172554;
}

html[dir="ltr"] .arrow-icon {
    transform: rotate(180deg);
}

@media (max-width: 768px) {
    .floating-cart-bar {
        width: calc(100% - 24px);
        padding: 12px 16px;
    }
    .cart-bar-content {
        flex-direction: column;
        align-items: stretch;
    }
    .cart-action-side {
        justify-content: space-between;
    }
}
</style>

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
            title: '{{ __("تنبيه") }}',
            text: '{{ __("حدد مادة دراسية واحدة على الأقل للاشتراك في المنهاج.") }}'
        });
        return;
    }

    const btn = document.getElementById('btnGoToCheckout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __("جاري المعالجة...") }}';

    const formData = new FormData(form);

    axios.post('{{ route("student.courses.checkout") }}', formData)
        .then(res => {
            window.location.href = res.data.redirect || '{{ route("student.checkout.show") }}';
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>{{ __("إتمام الدفع الفلسطيني") }}</span> <i class="fa-solid fa-arrow-left arrow-icon"></i>';
            const msg = err.response?.data?.message || '{{ __("حدث خطأ أثناء تجهيز الطلب، يرجى المحاولة ثانية.") }}';
            Swal.fire({ icon: 'error', title: '{{ __("خطأ") }}', text: msg });
        });
}
</script>
@endsection
