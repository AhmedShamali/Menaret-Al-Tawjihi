@extends('layouts.app')

@section('title', __('إدارة شريط آخر الأخبار العاجلة') . ' | ' . config('app.name', 'Step by Step'))

@section('content')
<div class="admin-news-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 10px 4px 60px 4px;">

    {{-- 1. رأس الصفحة والمسار الأكاديمي --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #fff1f2; color: #e11d48; display: grid; place-items: center; font-size: 1.4rem; border: 1px solid #fecdd3; flex-shrink: 0;">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h1 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0; font-family: 'Alexandria', 'Cairo', sans-serif;">
                        {{ __('إدارة شريط آخر الأخبار والتنبيهات العاجلة') }}
                    </h1>
                    @if($isEnabled)
                        <span id="global-status-badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 20px; font-size: 0.76rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                            {{ __('الشريط مفعّل بالصفحة الرئيسية') }}
                        </span>
                    @else
                        <span id="global-status-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 3px 10px; border-radius: 20px; font-size: 0.76rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; display: inline-block;"></span>
                            {{ __('الشريط معطّل مؤقتاً') }}
                        </span>
                    @endif
                </div>
                <div style="font-size: 0.83rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #64748b; text-decoration: none;">{{ __('الرئيسية') }}</a>
                    <span>/</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ __('شريط الأخبار') }}</span>
                    <span>•</span>
                    <span>{{ __('الأخبار والتنبيهات المباشرة التي تظهر في شريط الصفحة الرئيسية للزوار والطلاب') }}</span>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            {{-- زر تبديل تشغيل/إيقاف الشريط العام --}}
            <button type="button" id="btn-toggle-global" onclick="toggleGlobalTicker()" style="display: inline-flex; align-items: center; gap: 8px; background: {{ $isEnabled ? '#fef2f2' : '#f0fdf4' }}; color: {{ $isEnabled ? '#b91c1c' : '#15803d' }}; border: 1px solid {{ $isEnabled ? '#fecaca' : '#bbf7d0' }}; padding: 9px 16px; border-radius: 8px; font-weight: 700; font-size: 0.84rem; cursor: pointer; transition: all 0.2s;">
                <i class="fa-solid {{ $isEnabled ? 'fa-power-off' : 'fa-play' }}"></i>
                <span id="global-btn-text">{{ $isEnabled ? __('إيقاف الشريط العام') : __('تشغيل الشريط العام') }}</span>
            </button>

            {{-- زر معاينة الصفحة الرئيسية --}}
            <a href="{{ route('home') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 0.84rem; text-decoration: none;">
                <i class="fa-solid fa-arrow-up-right-from-square" style="color: #64748b;"></i>
                <span>{{ __('معاينة بالموقع') }}</span>
            </a>

            {{-- زر إضافة خبر جديد --}}
            <button type="button" onclick="openCreateModal()" style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #e11d48, #be123c); color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; cursor: pointer; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25); transition: transform 0.15s;">
                <i class="fa-solid fa-plus"></i>
                <span>{{ __('إضافة خبر عاجل') }}</span>
            </button>
        </div>
    </div>

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 0.88rem; font-weight: 600;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. صندوق المعاينة الحية للشريط --}}
    @php
        $activeCount = count(array_filter($items, fn($i) => !empty($i['is_active'])));
    @endphp
    <div style="background: linear-gradient(135deg, #072344 0%, #0d3868 100%); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; color: #ffffff; box-shadow: 0 4px 16px rgba(7, 35, 68, 0.12); position: relative; overflow: hidden; border: 1px solid rgba(217, 119, 6, 0.25);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: #d97706; color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 4px; letter-spacing: 0.5px;">
                    {{ __('معاينة حية') }}
                </span>
                <span style="font-size: 0.84rem; color: #cbd5e1;">
                    {{ __('كيف يظهر الشريط الآن لزوار منصة منارة التوجيهي:') }}
                </span>
            </div>
            <div style="font-size: 0.78rem; color: #94a3b8;">
                <span>{{ __('إجمالي الأخبار المسجلة:') }} <strong style="color: #f8fafc;">{{ count($items) }}</strong></span>
                <span style="margin: 0 6px;">•</span>
                <span>{{ __('المفعلة حالياً:') }} <strong style="color: #34d399;">{{ $activeCount }}</strong></span>
            </div>
        </div>

        {{-- شريط المعاينة التفاعلي الحي --}}
        @php
            $activeList = array_values(array_filter($items, fn($i) => !empty($i['is_active'])));
        @endphp
        <div style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; gap: 12px; overflow: hidden; position: relative;">
            <div style="background: linear-gradient(135deg, #e11d48, #be123c); color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 0.78rem; display: flex; align-items: center; gap: 6px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(225, 29, 72, 0.4);">
                <i class="fa-solid fa-bullhorn" style="font-size: 0.75rem;"></i>
                <span>{{ __('معاينة الشريط') }}</span>
            </div>

            <div style="flex: 1; min-width: 0; overflow: hidden; position: relative; height: 32px; display: flex; align-items: center;" id="adminPreviewViewport">
                @if(count($activeList) > 0)
                    @foreach($activeList as $pIdx => $pItem)
                        @php
                            $pType = $pItem['type'] ?? 'urgent';
                            $pBadgeStyles = [
                                'urgent'  => 'background: rgba(239,68,68,0.25); color: #fca5a5; border: 1px solid rgba(239,68,68,0.4);',
                                'warning' => 'background: rgba(217,119,6,0.25); color: #fde68a; border: 1px solid rgba(217,119,6,0.4);',
                                'info'    => 'background: rgba(14,165,233,0.25); color: #7dd3fc; border: 1px solid rgba(14,165,233,0.4);',
                                'success' => 'background: rgba(16,185,129,0.25); color: #86efac; border: 1px solid rgba(16,185,129,0.4);',
                            ];
                            $bStyle = $pBadgeStyles[$pType] ?? $pBadgeStyles['urgent'];
                        @endphp
                        <div class="admin-prev-item" data-prev-idx="{{ $pIdx }}" style="position: absolute; inset: 0; display: {{ $pIdx === 0 ? 'flex' : 'none' }}; align-items: center; gap: 10px; width: 100%;">
                            <span style="{{ $bStyle }} padding: 2px 8px; border-radius: 4px; font-size: 0.74rem; font-weight: 700; flex-shrink: 0;">
                                {{ $pItem['badge'] ?? 'عاجل' }}
                            </span>
                            <div class="admin-prev-text-wrap" style="flex: 1; min-width: 0; overflow: hidden; white-space: nowrap; display: flex; align-items: center;">
                                <span class="admin-prev-text" style="font-size: 0.88rem; color: #f1f5f9; font-weight: 600; white-space: nowrap; display: inline-block; width: max-content; will-change: transform;">
                                    {{ $pItem['text'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span style="color: #94a3b8; font-style: italic; font-size: 0.84rem;">{{ __('لا توجد أخبار مفعلة حالياً، الشريط لن يظهر بالصفحة الرئيسية.') }}</span>
                @endif
            </div>

            @if(count($activeList) > 1)
                <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                    <span id="adminPrevCounter" style="font-size: 0.72rem; color: #94a3b8; font-weight: 700; background: rgba(255,255,255,0.08); padding: 2px 6px; border-radius: 4px;">1 / {{ count($activeList) }}</span>
                    <button type="button" onclick="adminPrevNewsItem()" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; width: 24px; height: 24px; border-radius: 4px; cursor: pointer; display: grid; place-items: center; font-size: 0.7rem;" title="{{ __('الخبر السابق') }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <button type="button" onclick="adminNextNewsItem()" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; width: 24px; height: 24px; border-radius: 4px; cursor: pointer; display: grid; place-items: center; font-size: 0.7rem;" title="{{ __('الخبر التالي') }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- 3. قائمة الأخبار المسجلة --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); overflow: hidden;">
        <div style="padding: 16px 22px; background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: #0284c7;"></i>
                <strong style="color: #0f172a; font-size: 0.95rem;">
                    {{ __('قائمة الأخبار والتنبيهات المضافة') }}
                </strong>
                <span style="background: #e0f2fe; color: #0369a1; font-size: 0.78rem; font-weight: 700; padding: 2px 8px; border-radius: 12px;">
                    {{ count($items) }}
                </span>
            </div>
            <div style="font-size: 0.8rem; color: #64748b;">
                <i class="fa-regular fa-lightbulb" style="color: #d97706; margin-left: 4px;"></i>
                {{ __('الأخبار الأعلى تظهر أولاً في الشريط الدوار') }}
            </div>
        </div>

        @if(empty($items))
            <div style="padding: 60px 20px; text-align: center; color: #64748b;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: grid; place-items: center; margin: 0 auto 16px auto; font-size: 1.6rem; color: #94a3b8;">
                    <i class="fa-regular fa-newspaper"></i>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">{{ __('لم يتم إضافة أي أخبار حتى الآن') }}</h3>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 16px;">{{ __('قم بإضافة أول خبر عاجل ليظهر مباشرة للزوار والطلاب في أعلى الواجهة الرئيسية.') }}</p>
                <button type="button" onclick="openCreateModal()" style="display: inline-flex; align-items: center; gap: 6px; background: #e11d48; color: #ffffff; border: none; padding: 9px 18px; border-radius: 6px; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ __('إضافة أول خبر الآن') }}</span>
                </button>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; text-align: right;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700; font-size: 0.8rem;">
                            <th style="padding: 12px 18px; width: 60px; text-align: center;">#</th>
                            <th style="padding: 12px 18px; width: 140px;">{{ __('الوسم والنوع') }}</th>
                            <th style="padding: 12px 18px;">{{ __('نص الخبر') }}</th>
                            <th style="padding: 12px 18px; width: 140px;">{{ __('الرابط المرتبط') }}</th>
                            <th style="padding: 12px 18px; width: 120px; text-align: center;">{{ __('الحالة بالواجهة') }}</th>
                            <th style="padding: 12px 18px; width: 130px; text-align: center;">{{ __('تاريخ النشر') }}</th>
                            <th style="padding: 12px 18px; width: 130px; text-align: center;">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $item)
                            @php
                                $type = $item['type'] ?? 'urgent';
                                $typeStyles = [
                                    'urgent'  => ['bg' => '#fee2e2', 'color' => '#991b1b', 'border' => '#fca5a5', 'icon' => 'fa-circle-exclamation', 'label' => 'عاجل'],
                                    'warning' => ['bg' => '#fef3c7', 'color' => '#92400e', 'border' => '#fcd34d', 'icon' => 'fa-triangle-exclamation', 'label' => 'تنبيه'],
                                    'info'    => ['bg' => '#e0f2fe', 'color' => '#075985', 'border' => '#7dd3fc', 'icon' => 'fa-circle-info', 'label' => 'معلومات'],
                                    'success' => ['bg' => '#dcfce7', 'color' => '#166534', 'border' => '#86efac', 'icon' => 'fa-circle-check', 'label' => 'بشارة / نجاح'],
                                ];
                                $st = $typeStyles[$type] ?? $typeStyles['urgent'];
                                $isActive = !empty($item['is_active']);
                            @endphp
                            <tr id="row-{{ $item['id'] }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s; background: {{ $isActive ? '#ffffff' : '#fafafa' }};">
                                <td style="padding: 14px 18px; text-align: center; color: #94a3b8; font-weight: 700;">
                                    {{ $idx + 1 }}
                                </td>

                                <td style="padding: 14px 18px;">
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                        <span style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; border: 1px solid {{ $st['border'] }}; padding: 3px 9px; border-radius: 6px; font-size: 0.74rem; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid {{ $st['icon'] }}" style="font-size: 0.7rem;"></i>
                                            <span>{{ $item['badge'] ?? 'عاجل' }}</span>
                                        </span>
                                        <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">
                                            {{ $st['label'] }}
                                        </span>
                                    </div>
                                </td>

                                <td style="padding: 14px 18px;">
                                    <div style="font-weight: 600; color: {{ $isActive ? '#1e293b' : '#64748b' }}; line-height: 1.6; font-size: 0.88rem;">
                                        {{ $item['text'] }}
                                    </div>
                                </td>

                                <td style="padding: 14px 18px;">
                                    @if(!empty($item['url']))
                                        <a href="{{ $item['url'] }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; color: #0284c7; text-decoration: none; font-size: 0.78rem; font-weight: 600; background: #f0f9ff; padding: 4px 10px; border-radius: 6px; border: 1px solid #bae6fd;">
                                            <i class="fa-solid fa-link"></i>
                                            <span>{{ __('فتح الرابط') }}</span>
                                        </a>
                                    @else
                                        <span style="color: #cbd5e1; font-size: 0.78rem;">{{ __('بدون رابط') }}</span>
                                    @endif
                                </td>

                                <td style="padding: 14px 18px; text-align: center;">
                                    <button type="button" onclick="toggleItemStatus('{{ $item['id'] }}')" id="toggle-btn-{{ $item['id'] }}" style="border: none; background: transparent; cursor: pointer; padding: 4px 8px; border-radius: 20px; transition: all 0.2s;" title="{{ $isActive ? __('إيقاف العرض') : __('تفعيل العرض') }}">
                                        @if($isActive)
                                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 12px; border-radius: 20px; font-size: 0.74rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-check-circle"></i>
                                                <span>{{ __('معروض') }}</span>
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; padding: 4px 12px; border-radius: 20px; font-size: 0.74rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                                <i class="fa-solid fa-eye-slash"></i>
                                                <span>{{ __('مخفي') }}</span>
                                            </span>
                                        @endif
                                    </button>
                                </td>

                                <td style="padding: 14px 18px; text-align: center; color: #64748b; font-size: 0.78rem;">
                                    <div>{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('Y-m-d') }}</div>
                                    <div style="font-size: 0.72rem; color: #94a3b8;">{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('H:i') }}</div>
                                </td>

                                <td style="padding: 14px 18px; text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button type="button" 
                                            class="btn-edit-news"
                                            data-id="{{ $item['id'] }}"
                                            data-text="{{ e($item['text'] ?? '') }}"
                                            data-badge="{{ e($item['badge'] ?? 'عاجل') }}"
                                            data-type="{{ e($item['type'] ?? 'urgent') }}"
                                            data-url="{{ e($item['url'] ?? '') }}"
                                            data-active="{{ !empty($item['is_active']) ? '1' : '0' }}"
                                            onclick="openEditNewsModalFromButton(this)" 
                                            style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: grid; place-items: center; font-size: 0.82rem; transition: all 0.15s;" 
                                            title="{{ __('تعديل الخبر') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" onclick="deleteNewsItem('{{ $item['id'] }}')" style="background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: grid; place-items: center; font-size: 0.82rem; transition: all 0.15s;" title="{{ __('حذف الخبر') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- مودال إضافة وتعديل الخبر --}}
<div id="news-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #ffffff; border-radius: 14px; max-width: 580px; width: 100%; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); overflow: hidden; animation: modalPop 0.25s ease-out;">
        
        {{-- رأس المودال --}}
        <div style="padding: 18px 24px; background: #072344; color: #ffffff; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #d97706;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(217, 119, 6, 0.2); color: #fbbf24; display: grid; place-items: center; font-size: 1.1rem; border: 1px solid rgba(217, 119, 6, 0.4);">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h3 id="modal-title" style="margin: 0; font-size: 1.05rem; font-weight: 800; font-family: 'Alexandria', 'Cairo', sans-serif;">
                        {{ __('إضافة خبر عاجل جديد') }}
                    </h3>
                    <div style="font-size: 0.74rem; color: #94a3b8;">
                        {{ __('سيظهر مباشرة في الشريط الإخباري بالصفحة الرئيسية') }}
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeNewsModal()" style="background: rgba(255, 255, 255, 0.1); border: none; color: #ffffff; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; display: grid; place-items: center; font-size: 0.9rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- جسم المودال --}}
        <form id="news-form" method="POST" action="{{ route('admin.news.store') }}" style="padding: 24px;">
            @csrf
            <input type="hidden" name="id" id="form-item-id" value="">

            {{-- نص الخبر --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 6px;">
                    {{ __('نص الخبر العاجل أو الإعلان') }} <span style="color: #e11d48;">*</span>
                </label>
                <textarea name="text" id="form-item-text" rows="3" required placeholder="{{ __('اكتب تفاصيل الخبر هنا، مثال: بدء التسجيل للمكثفات الوزارية الشاملة لطلبة التوجيهي لعام 2026...') }}" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; font-family: inherit; line-height: 1.5; resize: vertical; box-sizing: border-box;"></textarea>
                <div style="font-size: 0.74rem; color: #64748b; margin-top: 4px;">
                    {{ __('اجعل النص واضحاً وموجزاً لتحقيق أفضل تجربة قراءة في الشريط المتحرك.') }}
                </div>
            </div>

            {{-- الوسم والنوع في سطرين --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                {{-- الوسم --}}
                <div>
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 6px;">
                        {{ __('وسم التصنيف') }} <span style="color: #e11d48;">*</span>
                    </label>
                    <input type="text" name="badge" id="form-item-badge" value="عاجل" required placeholder="{{ __('مثل: عاجل، إعلان، توجيهي 2026') }}" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; box-sizing: border-box;">
                    
                    {{-- مقترحات سريعة للوسم --}}
                    <div style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 6px;">
                        <button type="button" onclick="setQuickBadge('عاجل')" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; cursor: pointer;">عاجل</button>
                        <button type="button" onclick="setQuickBadge('إعلان توجيهي 2026')" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; cursor: pointer;">توجيهي 2026</button>
                        <button type="button" onclick="setQuickBadge('تنبيه وزاري')" style="background: #e0f2fe; color: #075985; border: 1px solid #7dd3fc; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; cursor: pointer;">تنبيه وزاري</button>
                    </div>
                </div>

                {{-- النوع واللون --}}
                <div>
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 6px;">
                        {{ __('نوع الخبر والمظهر') }} <span style="color: #e11d48;">*</span>
                    </label>
                    <select name="type" id="form-item-type" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.85rem; background: #ffffff; box-sizing: border-box;">
                        <option value="urgent">🔴 عاجل وخطير (أحمر)</option>
                        <option value="warning" selected>🟡 تنبيه وإعلان رسمي (ذهبي / كهرماني)</option>
                        <option value="info">🔵 معلومة وتحديث أكاديمي (أزرق)</option>
                        <option value="success">🟢 تهنئة وبشارة نجاح (أخضر)</option>
                    </select>
                </div>
            </div>

            {{-- الرابط المرتبط --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #1e293b; margin-bottom: 6px;">
                    {{ __('رابط التفاصيل (اختياري)') }}
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-link" style="position: absolute; right: 12px; top: 12px; color: #94a3b8; font-size: 0.85rem;"></i>
                    <input type="url" name="url" id="form-item-url" placeholder="https://... أو رابط داخلي" style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 9px 34px 9px 12px; font-size: 0.85rem; box-sizing: border-box;">
                </div>
                <div style="font-size: 0.74rem; color: #64748b; margin-top: 4px;">
                    {{ __('عند النقر على الخبر في الواجهة الرئيسية، سيتم فتح هذا الرابط.') }}
                </div>
            </div>

            {{-- خيارات إضافية --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 22px; display: flex; flex-direction: column; gap: 10px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #1e293b; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="is_active" id="form-item-active" value="1" checked style="width: 16px; height: 16px; accent-color: #059669;">
                    <span>{{ __('تفعيل هذا الخبر مباشرة في شريط الواجهة الرئيسية') }}</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #1e293b; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="notify_students" id="form-item-notify" value="1" style="width: 16px; height: 16px; accent-color: #e11d48;">
                    <span>{{ __('إرسال إشعار فوري لجميع طلبة التوجيهي عبر المنصة 🔔') }}</span>
                </label>
            </div>

            {{-- أزرار الإجراءات --}}
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeNewsModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    {{ __('إلغاء') }}
                </button>
                <button type="submit" id="btn-save-news" style="background: linear-gradient(135deg, #072344, #1e40af); color: #ffffff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 0.86rem; cursor: pointer; box-shadow: 0 2px 8px rgba(7, 35, 68, 0.25);">
                    <i class="fa-solid fa-floppy-disk" style="margin-left: 6px;"></i>
                    <span id="btn-save-text">{{ __('حفظ ونشر الخبر') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

function setQuickBadge(text) {
    document.getElementById('form-item-badge').value = text;
}

function openCreateModal() {
    document.getElementById('news-form').reset();
    document.getElementById('form-item-id').value = '';
    document.getElementById('modal-title').textContent = '{{ __("إضافة خبر عاجل جديد") }}';
    document.getElementById('btn-save-text').textContent = '{{ __("حفظ ونشر الخبر") }}';
    document.getElementById('form-item-active').checked = true;
    document.getElementById('form-item-notify').checked = false;
    document.getElementById('news-modal').style.display = 'flex';
}

function openEditNewsModalFromButton(btn) {
    if (!btn) return;
    const ds = btn.dataset;
    openEditModal({
        id: ds.id,
        text: ds.text,
        badge: ds.badge,
        type: ds.type,
        url: ds.url,
        is_active: ds.active === '1'
    });
}

function openEditModal(item) {
    if (!item) return;
    if (item instanceof HTMLElement) {
        return openEditNewsModalFromButton(item);
    }
    document.getElementById('form-item-id').value = item.id || '';
    document.getElementById('form-item-text').value = item.text || '';
    document.getElementById('form-item-badge').value = item.badge || 'عاجل';
    document.getElementById('form-item-type').value = item.type || 'urgent';
    document.getElementById('form-item-url').value = item.url || '';
    document.getElementById('form-item-active').checked = Boolean(item.is_active);
    document.getElementById('form-item-notify').checked = false;

    document.getElementById('modal-title').textContent = '{{ __("تعديل الخبر") }}';
    document.getElementById('btn-save-text').textContent = '{{ __("حفظ التعديلات") }}';
    document.getElementById('news-modal').style.display = 'flex';
}

function closeNewsModal() {
    document.getElementById('news-modal').style.display = 'none';
}

// إغلاق المودال عند النقر في الخارج
window.addEventListener('click', function(e) {
    const modal = document.getElementById('news-modal');
    if (e.target === modal) {
        closeNewsModal();
    }
});

// تبديل تفعيل الشريط العام
function toggleGlobalTicker() {
    const btn = document.getElementById('btn-toggle-global');
    btn.disabled = true;

    fetch('{{ route("admin.news.toggleGlobal") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء تبديل حالة الشريط.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        console.error(err);
        window.location.reload();
    });
}

// تبديل حالة خبر محدد
function toggleItemStatus(id) {
    const btn = document.getElementById('toggle-btn-' + id);
    if (btn) btn.disabled = true;

    fetch('{{ url("admin/news") }}/' + id + '/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (data.success) {
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء تحديث حالة الخبر.');
        }
    })
    .catch(err => {
        if (btn) btn.disabled = false;
        window.location.reload();
    });
}

// حذف خبر
function deleteNewsItem(id) {
    if (!confirm('{{ __("هل أنت متأكد من حذف هذا الخبر نهائياً من شريط الأخبار؟") }}')) {
        return;
    }

    fetch('{{ url("admin/news") }}/' + id, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء حذف الخبر.');
        }
    })
    .catch(err => {
        window.location.reload();
    });
}

// تشغيل شريط المعاينة التفاعلي في لوحة التحكم مع تحريك ناعم للأخبار الطويلة
(function() {
    const prevItems = document.querySelectorAll('.admin-prev-item');
    if (!prevItems.length) return;

    let pCurrent = 0;
    const pCounter = document.getElementById('adminPrevCounter');

    function showItem(idx) {
        prevItems.forEach((el, i) => {
            el.style.display = (i === idx) ? 'flex' : 'none';
            const t = el.querySelector('.admin-prev-text');
            if (t) {
                t.style.transition = 'none';
                t.style.transform = 'translateX(0)';
            }
        });
        pCurrent = idx;
        if (pCounter) pCounter.textContent = (pCurrent + 1) + ' / ' + prevItems.length;

        // تدفق النص الطويل تلقائياً
        const cur = prevItems[pCurrent];
        if (cur) {
            const wrap = cur.querySelector('.admin-prev-text-wrap');
            const text = cur.querySelector('.admin-prev-text');
            if (wrap && text) {
                requestAnimationFrame(() => {
                    const wrapW = wrap.clientWidth;
                    const textW = Math.max(text.scrollWidth, text.getBoundingClientRect().width);
                    const diff = textW - wrapW;
                    if (diff > 8) {
                        const sec = Math.max(3.2, (diff + 25) / 36);
                        setTimeout(() => {
                            text.style.transition = 'transform ' + sec + 's linear';
                            text.style.transform = 'translateX(' + (diff + 25) + 'px)';
                        }, 1200);
                    }
                });
            }
        }
    }

    window.adminNextNewsItem = function() {
        showItem((pCurrent + 1) % prevItems.length);
    };

    window.adminPrevNewsItem = function() {
        showItem((pCurrent - 1 + prevItems.length) % prevItems.length);
    };

    showItem(0);
})();
</script>
@endsection
