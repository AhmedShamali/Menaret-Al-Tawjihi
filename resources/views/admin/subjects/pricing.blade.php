@extends('layouts.app')

@section('title', 'إدارة أسعار المواد والعروض الترويجية')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; animation: fadeIn 0.5s ease;">

    <!-- الترويسة -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                <i class="fa-solid fa-tags" style="color: #0284c7;"></i> إدارة تسعير المواد وباقات الاشتراك
            </h1>
            <p style="color: #64748b; font-size: 0.88rem; margin: 0;">
                تحديد أسعار المواد بالشيكل (₪)، ضبط الخصومات الترويجية، وتفعيل المواد المجانية لطلبة توجيهي فلسطين.
            </p>
        </div>

        <button onclick="openSeasonalModal()" style="background: linear-gradient(135deg, #059669, #10b981); color: white; border: none; padding: 11px 22px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);">
            <i class="fa-solid fa-percent"></i>{{ __('تطبيق خصم موسمي شامل') }}</button>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 20px; border-radius: 14px; margin-bottom: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- كروت الإحصائيات الكلاسيكية -->
    <div class="stats-row-clean">
        <div class="stat-card-clean" style="--card-accent: #1e3a8a;">
            <span class="stat-label">{{ __('إجمالي المواد الدراسية') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-navy">{{ $pricingStats['total_subjects'] }}</span>
                <i class="fa-solid fa-book-bookmark stat-icon text-navy"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #d97706;">
            <span class="stat-label">{{ __('مواد عليها عروض مخفضة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-amber">{{ $pricingStats['discounted'] }}</span>
                <i class="fa-solid fa-fire stat-icon text-amber"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #059669;">
            <span class="stat-label">مواد مجانية / تجريبية</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-emerald">{{ $pricingStats['free_subjects'] }}</span>
                <i class="fa-solid fa-gift stat-icon text-emerald"></i>
            </div>
        </div>

        <div class="stat-card-clean" style="--card-accent: #6366f1;">
            <span class="stat-label">{{ __('متوسط سعر المادة') }}</span>
            <div class="stat-value-wrap">
                <span class="stat-number text-indigo">{{ round($pricingStats['avg_price']) }} ₪</span>
                <i class="fa-solid fa-shekel-sign stat-icon text-indigo"></i>
            </div>
        </div>
    </div>

    <!-- فلتر المرحلة النظيف -->
    <div class="toolbar-clean">
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-filter" style="color: #64748b; font-size: 0.85rem;"></i>
            <span style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">{{ __('تصفية حسب الفرع:') }}</span>
        </div>
        <div class="filter-pills-clean">
            <a href="{{ route('admin.subjects.pricing') }}" class="filter-pill {{ empty($stageId) ? 'active' : '' }}">{{ __('جميع الفروع') }}</a>
            @foreach($stages as $stg)
                <a href="{{ route('admin.subjects.pricing', ['stage_id' => $stg->id]) }}" class="filter-pill {{ $stageId == $stg->id ? 'active' : '' }}">
                    {{ $stg->name ?? $stg->name_ar ?? 'المرحلة' }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- جدول الأسعار الكلاسيكي الأكاديمي -->
    <div class="table-card-clean">
        <div class="table-container-clean">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th>{{ __('المادة الدراسية') }}</th>
                        <th style="width: 130px;">{{ __('الفرع الأكاديمي') }}</th>
                        <th style="width: 120px;">{{ __('السعر الأساسي') }}</th>
                        <th style="width: 110px;">{{ __('نسبة الخصم') }}</th>
                        <th style="width: 130px;">{{ __('السعر بعد الخصم') }}</th>
                        <th style="width: 160px;">{{ __('الحالة والتسعير الفعلي') }}</th>
                        <th style="width: 120px; text-align: center;">{{ __('الإجراء') }}</th>
                    </tr>
                </thead>
            <tbody>
                @forelse($subjects as $sub)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $sub->color ?? '#0284c7' }}15; color: {{ $sub->color ?? '#0284c7' }}; display: grid; place-items: center; font-size: 1.05rem; flex-shrink: 0;">
                                    <i class="fa-solid {{ $sub->icon ?? 'fa-book' }}"></i>
                                </div>
                                <div>
                                    <strong style="color: #0f172a; font-size: 0.95rem;">{{ $sub->name_ar }}</strong>
                                    <small style="color: #94a3b8; display: block; font-size: 0.74rem;">{{ $sub->contents_count ?? 0 }} درس • {{ $sub->exams_count ?? 0 }} اختبار</small>
                                </div>
                            </div>
                        </td>

                        <td style="padding: 16px 20px;">
                            <span style="background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700;">
                                {{ optional($sub->stage)->name ?? optional($sub->stage)->name_ar ?? 'توجيهي' }}
                            </span>
                        </td>

                        {{-- السعر الأساسي --}}
                        <td style="padding: 16px 20px; font-family: monospace; font-size: 0.95rem; font-weight: 700; color: #334155;">
                            {{ number_format($sub->price_ils ?? 150, 2) }} ₪
                        </td>

                        {{-- نسبة الخصم --}}
                        <td style="padding: 16px 20px; font-family: monospace; font-size: 0.92rem; font-weight: 800;">
                            @if($sub->is_free)
                                <span style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 3px 8px; border-radius: 6px;">
                                    100%
                                </span>
                            @elseif($sub->has_discount)
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-arrow-down" style="font-size: 0.7rem;"></i> {{ $sub->discount_percentage }}%
                                </span>
                            @else
                                <span style="color: #94a3b8; font-size: 0.85rem;">0%</span>
                            @endif
                        </td>

                        {{-- السعر بعد الخصم --}}
                        <td style="padding: 16px 20px; font-family: monospace; font-size: 1rem; font-weight: 800;">
                            @if($sub->is_free)
                                <span style="color: #15803d;">0.00 ₪ <small style="font-size: 0.72rem;">(مجاناً)</small></span>
                            @elseif($sub->has_discount)
                                <span style="color: #ea580c;">{{ number_format($sub->price_after_discount, 2) }} ₪</span>
                            @else
                                <span style="color: #475569;">{{ number_format($sub->price_ils ?? 150, 2) }} ₪</span>
                            @endif
                        </td>

                        {{-- الحالة والتسعير الفعلي --}}
                        <td style="padding: 16px 20px;">
                            @if($sub->is_free)
                                <span style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 16px; font-size: 0.76rem; font-weight: 800;">
                                    مجانية تجريبية 🎁
                                </span>
                            @elseif($sub->has_discount)
                                <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 16px; font-size: 0.76rem; font-weight: 800;">
                                    خصم {{ $sub->discount_percentage }}% • {{ number_format($sub->price_after_discount, 0) }} ₪
                                </span>
                            @else
                                <span style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 16px; font-size: 0.76rem; font-weight: 700;">
                                    سعر عادي: {{ number_format($sub->price_ils, 0) }} ₪
                                </span>
                            @endif
                        </td>

                        <td style="padding: 16px 20px; text-align: center;">
                            <button onclick="editPricing({{ json_encode($sub->append(['has_discount', 'discount_percentage', 'price_after_discount'])) }})" style="background: #f8fafc; color: #1e3a8a; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f8fafc'">
                                <i class="fa-solid fa-pen-to-square"></i> {{ __('تعديل السعر') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">{{ __('لا توجد مواد مسجلة مطابقة للبحث.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

</div>

<!-- نافذة تعديل تسعيرة مادة (Modal) بتصميم كلاسيكي وحاسبة تفاعلية متبادلة -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; max-width: 520px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid #cbd5e1; animation: zoomIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: grid; place-items: center; font-size: 1.1rem;">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div>
                    <h3 id="modalSubjectTitle" style="font-size: 1.12rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('تعديل تسعيرة المادة') }}</h3>
                    <small style="color: #64748b; font-size: 0.76rem;">{{ __('حاسبة تفاعلية لحساب نسبة الخصم والسعر النهائي') }}</small>
                </div>
            </div>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="editPricingForm" onsubmit="submitPricing(event)">
            @csrf
            <input type="hidden" id="editSubjectId" name="subject_id">

            {{-- 1. السعر الأساسي --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 6px;">{{ __('السعر الأساسي بالشيكل (₪) *') }}</label>
                <div style="position: relative;">
                    <input type="number" step="1" min="0" id="modalPrice" name="price_ils" required oninput="onBasePriceChange()" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1rem; font-weight: 800; font-family: monospace; color: #0f172a;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 800;">₪</span>
                </div>
            </div>

            {{-- 2. نسبة الخصم والسعر بعد الخصم تفاعليين جنباً إلى جنب --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fa-solid fa-percent text-amber"></i> {{ __('نسبة الخصم (%)') }}
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="1" min="0" max="100" id="modalDiscountPercent" name="discount_percentage" placeholder="0" oninput="onDiscountPercentChange()" style="width: 100%; padding: 10px 14px 10px 34px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.95rem; font-weight: 800; font-family: monospace; color: #dc2626;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #dc2626; font-weight: 800;">%</span>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        <i class="fa-solid fa-coins text-emerald"></i> {{ __('السعر بعد الخصم (₪)') }}
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.5" min="0" id="modalDiscountPrice" name="discount_price_ils" placeholder="نفس الأساسي" oninput="onDiscountPriceChange()" style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 0.95rem; font-weight: 800; font-family: monospace; color: #ea580c;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #ea580c; font-weight: 800;">₪</span>
                    </div>
                </div>
            </div>

            {{-- بطاقة توضيحية لملخص الحساب الحي --}}
            <div id="liveCalcBox" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem;">
                <span style="color: #64748b; font-weight: 600;">{{ __('قيمة التوفير للطالب:') }} <strong class="font-mono text-rose font-bold" id="previewSavedAmount">0 ₪</strong></span>
                <span style="color: #334155; font-weight: 700;">{{ __('السعر النهائي المقرر:') }} <strong class="font-mono text-emerald font-bold" style="font-size: 1.05rem;" id="previewFinalPrice">150 ₪</strong></span>
            </div>

            {{-- 3. خيار مجانية المادة --}}
            <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px; background: #f0fdf4; padding: 10px 14px; border-radius: 10px; border: 1px solid #bbf7d0;">
                <input type="checkbox" id="modalIsFree" name="is_free" value="1" onchange="onIsFreeChange()" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="modalIsFree" style="font-size: 0.84rem; font-weight: 700; color: #166534; cursor: pointer;">
                    {{ __('تعيين المادة كمجانية بالكامل لكافة الطلاب (0 ₪)') }} 🎁
                </label>
            </div>

            {{-- 4. وصف الباقة والمميزات --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('وصف باقة المادة ومميزاتها للطلاب') }}</label>
                <textarea id="modalDescription" name="description" rows="2" placeholder="{{ __('مثال: تشمل شرح كامل المنهاج الوزاري، حلول أسئلة السنوات السابقة، وبطاقات المذاكرة السريعة') }}" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #cbd5e1; outline: none; font-size: 0.85rem; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">{{ __('إلغاء') }}</button>
                <button type="submit" id="btnSavePrice" style="background: #1e3a8a; color: white; border: none; padding: 9px 24px; border-radius: 8px; font-weight: 800; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-check"></i> {{ __('حفظ وتطبيق التسعيرة') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة الخصم الشامل (Seasonal Modal) -->
<div id="seasonalModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; max-width: 480px; width: 100%; padding: 26px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid #cbd5e1;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">{{ __('تطبيق خصم موسمي موحد') }}</h3>
            <button onclick="closeSeasonalModal()" style="background: none; border: none; font-size: 1.3rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.subjects.pricing.seasonal') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('نسبة الخصم المئوية (%) *') }}</label>
                <input type="number" name="discount_percentage" min="5" max="90" value="20" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; outline: none; font-size: 1.1rem; font-weight: 800; color: #059669; font-family: monospace;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 6px;">{{ __('تطبيق الخصم على فرع محدد (اختياري)') }}</label>
                <select name="stage_id" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid #cbd5e1; outline: none; font-size: 0.88rem;">
                    <option value="">{{ __('جميع فروع الثانوية العامة') }}</option>
                    @foreach($stages as $stg)
                        <option value="{{ $stg->id }}">{{ $stg->name ?? $stg->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeSeasonalModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">{{ __('إلغاء') }}</button>
                <button type="submit" style="background: #059669; color: white; border: none; padding: 9px 24px; border-radius: 8px; font-weight: 800; cursor: pointer;">{{ __('تطبيق الخصم فوراً') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSubject = null;

function editPricing(sub) {
    currentSubject = sub;
    document.getElementById('editSubjectId').value = sub.id;
    document.getElementById('modalSubjectTitle').textContent = 'تعديل تسعيرة: ' + sub.name_ar;
    
    const basePrice = parseFloat(sub.price_ils) || 150;
    const discountPrice = (sub.discount_price_ils !== null && parseFloat(sub.discount_price_ils) > 0) ? parseFloat(sub.discount_price_ils) : '';
    const isFree = (sub.is_free == 1);
    
    document.getElementById('modalPrice').value = basePrice;
    document.getElementById('modalDiscountPrice').value = discountPrice;
    document.getElementById('modalIsFree').checked = isFree;
    document.getElementById('modalDescription').value = sub.description || '';

    if (discountPrice && basePrice > 0 && discountPrice < basePrice) {
        const pct = Math.round(((basePrice - discountPrice) / basePrice) * 100);
        document.getElementById('modalDiscountPercent').value = pct;
    } else {
        document.getElementById('modalDiscountPercent').value = '';
    }

    updateLiveCalcBox();

    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}

function onBasePriceChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const pct = parseFloat(document.getElementById('modalDiscountPercent').value) || 0;

    if (pct > 0 && basePrice > 0) {
        const after = Math.round((basePrice * (1 - (pct / 100))) * 100) / 100;
        document.getElementById('modalDiscountPrice').value = after;
    }
    updateLiveCalcBox();
}

function onDiscountPercentChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const pct = parseFloat(document.getElementById('modalDiscountPercent').value);

    if (basePrice > 0 && !isNaN(pct) && pct >= 0 && pct <= 100) {
        if (pct === 0) {
            document.getElementById('modalDiscountPrice').value = '';
        } else {
            const after = Math.round((basePrice * (1 - (pct / 100))) * 100) / 100;
            document.getElementById('modalDiscountPrice').value = after;
        }
    }
    updateLiveCalcBox();
}

function onDiscountPriceChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    if (isFree) return;

    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const after = parseFloat(document.getElementById('modalDiscountPrice').value);

    if (basePrice > 0 && !isNaN(after) && after >= 0) {
        if (after >= basePrice) {
            document.getElementById('modalDiscountPercent').value = '0';
        } else {
            const pct = Math.round(((basePrice - after) / basePrice) * 100);
            document.getElementById('modalDiscountPercent').value = pct;
        }
    } else if (isNaN(after) || after === 0) {
        document.getElementById('modalDiscountPercent').value = '';
    }
    updateLiveCalcBox();
}

function onIsFreeChange() {
    const isFree = document.getElementById('modalIsFree').checked;
    const pInput = document.getElementById('modalPrice');
    const pctInput = document.getElementById('modalDiscountPercent');
    const afterInput = document.getElementById('modalDiscountPrice');

    if (isFree) {
        pctInput.value = '100';
        afterInput.value = '0';
        pctInput.disabled = true;
        afterInput.disabled = true;
    } else {
        pctInput.disabled = false;
        afterInput.disabled = false;
        onBasePriceChange();
    }
    updateLiveCalcBox();
}

function updateLiveCalcBox() {
    const isFree = document.getElementById('modalIsFree').checked;
    const basePrice = parseFloat(document.getElementById('modalPrice').value) || 0;
    const after = parseFloat(document.getElementById('modalDiscountPrice').value);

    let finalPrice = basePrice;
    let saved = 0;

    if (isFree) {
        finalPrice = 0;
        saved = basePrice;
    } else if (!isNaN(after) && after > 0 && after < basePrice) {
        finalPrice = after;
        saved = Math.max(0, basePrice - after);
    }

    document.getElementById('previewSavedAmount').innerText = saved.toFixed(2) + ' ₪';
    document.getElementById('previewFinalPrice').innerText = finalPrice.toFixed(2) + ' ₪';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'flex';
}

function closeSeasonalModal() {
    document.getElementById('seasonalModal').style.display = 'none';
}

function submitPricing(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSavePrice');
    const id = document.getElementById('editSubjectId').value;
    const price = document.getElementById('modalPrice').value;
    const discountPrice = document.getElementById('modalDiscountPrice').value;
    const discountPercent = document.getElementById('modalDiscountPercent').value;
    const isFree = document.getElementById('modalIsFree').checked ? 1 : 0;
    const desc = document.getElementById('modalDescription').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}';

    axios.post(`/admin/subjects/pricing/${id}/update`, {
        price_ils: price,
        discount_price_ils: discountPrice,
        discount_percentage: discountPercent,
        is_free: isFree,
        description: desc
    })
    .then(res => {
        closeEditModal();
        Swal.fire({
            icon: 'success',
            title: '{{ __('تم التحديث بنجاح!') }}',
            text: res.data.message || '{{ __('تم تحديث تسعيرة المادة بنجاح.') }}',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> {{ __('حفظ وتطبيق التسعيرة') }}';
        const msg = err.response?.data?.message || '{{ __('تعذر تحديث التسعيرة.') }}';
        Swal.fire({ icon: 'error', title: '{{ __('خطأ') }}', text: msg });
    });
}
</script>
@endsection
