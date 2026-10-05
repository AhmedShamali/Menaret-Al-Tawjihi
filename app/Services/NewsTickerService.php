<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Str;

class NewsTickerService
{
    const SETTING_KEY = 'news_ticker_items';
    const STATUS_KEY = 'news_ticker_enabled';

    /**
     * هل شريط الأخبار مفعل عاماً بالصفحة الرئيسية؟
     */
    public static function isEnabled(): bool
    {
        $val = Setting::get(self::STATUS_KEY, '1');
        return $val === '1' || $val === 1 || $val === true || $val === 'true';
    }

    /**
     * تفعيل أو تعطيل الشريط بالكامل
     */
    public static function setEnabled(bool $enabled): bool
    {
        Setting::set(self::STATUS_KEY, $enabled ? '1' : '0');
        return $enabled;
    }

    /**
     * جلب كافة الأخبار المسجلة (للمدير)
     */
    public static function getAll(): array
    {
        $raw = Setting::get(self::SETTING_KEY);
        if (empty($raw)) {
            // بيانات افتراضية أولية مميزة
            return [
                [
                    'id'         => 'news_init_1',
                    'text'       => 'أهلاً وسهلاً بكافة طلبة الثانوية العامة في فلسطين (القدس، الضفة الغربية، وقطاع غزة). المنظومة مفتوحة للتسجيل وتفعيل الشروحات والتدريبات التفاعلية المعتمدة لعام 2026.',
                    'badge'      => 'إعلان توجيهي 2026',
                    'type'       => 'warning', // 'urgent' | 'warning' | 'info' | 'success'
                    'url'        => '',
                    'is_active'  => true,
                    'created_at' => now()->format('Y-m-d H:i'),
                ],
                [
                    'id'         => 'news_init_2',
                    'text'       => 'تم اعتماد بنك الامتحانات الوزارية الإلكترونية التجريبية لجميع الفروع الأكاديمية تحت إشراف م.أحمد شمالي.',
                    'badge'      => 'هام جداً',
                    'type'       => 'urgent',
                    'url'        => route('courses.catalog'),
                    'is_active'  => true,
                    'created_at' => now()->format('Y-m-d H:i'),
                ]
            ];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }

    /**
     * جلب الأخبار المفعلة فقط للواجهة الرئيسية
     */
    public static function getActive(): array
    {
        if (!self::isEnabled()) {
            return [];
        }

        $all = self::getAll();
        return array_values(array_filter($all, function ($item) {
            return !empty($item['is_active']) && !empty(trim($item['text'] ?? ''));
        }));
    }

    /**
     * إضافة أو تعديل خبر
     */
    public static function saveItem(array $data, ?string $id = null): array
    {
        $items = self::getAll();
        $isNew = empty($id);
        $targetId = $isNew ? ('news_' . time() . '_' . Str::random(6)) : $id;

        $itemData = [
            'id'         => $targetId,
            'text'       => trim($data['text'] ?? ''),
            'badge'      => trim($data['badge'] ?? 'عاجل'),
            'type'       => in_array($data['type'] ?? '', ['urgent', 'warning', 'info', 'success']) ? $data['type'] : 'urgent',
            'url'        => trim($data['url'] ?? ''),
            'is_active'  => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            'created_at' => $data['created_at'] ?? now()->format('Y-m-d H:i'),
        ];

        if ($isNew) {
            // يضاف في البداية ليكون أحدث خبر
            array_unshift($items, $itemData);
        } else {
            $found = false;
            foreach ($items as &$existing) {
                if (($existing['id'] ?? '') === $targetId) {
                    $itemData['created_at'] = $existing['created_at'] ?? $itemData['created_at'];
                    $existing = $itemData;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_unshift($items, $itemData);
            }
        }

        self::saveAll($items);
        return $itemData;
    }

    /**
     * حذف خبر محدد
     */
    public static function deleteItem(string $id): bool
    {
        $items = self::getAll();
        $filtered = array_values(array_filter($items, function ($item) use ($id) {
            return ($item['id'] ?? '') !== $id;
        }));

        self::saveAll($filtered);
        return true;
    }

    /**
     * تبديل حالة خبر (تفعيل / إيقاف)
     */
    public static function toggleItem(string $id): bool
    {
        $items = self::getAll();
        $newStatus = false;
        foreach ($items as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['is_active'] = !($item['is_active'] ?? false);
                $newStatus = $item['is_active'];
                break;
            }
        }
        self::saveAll($items);
        return $newStatus;
    }

    /**
     * حفظ القائمة في جدول الإعدادات
     */
    protected static function saveAll(array $items): void
    {
        Setting::set(self::SETTING_KEY, json_encode($items, JSON_UNESCAPED_UNICODE));
    }
}
