<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['key', 'value'];
    protected static array $cache = [];
    protected static bool $dbAvailable = true;

    public static function get($key, $default = null) {
        if ($key === 'site_name' || $key === 'site_title') {
            return 'Step by Step';
        }

        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key] ?? $default;
        }

        if (!static::$dbAvailable) {
            return $default;
        }

        try {
            $setting = self::where('key', $key)->first();
            $val = $setting ? $setting->value : $default;
            if (is_string($val)) {
                $val = str_replace(['منارة التوجيهي', 'Stepvoro'], 'Step by Step', $val);
            }
            static::$cache[$key] = $val;
            return $val;
        } catch (\Throwable $e) {
            static::$dbAvailable = false;
            return $default;
        }
    }

    public static function set($key, $value) {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get the current academic year according to the active calendar.
     * In Palestine/Arab educational system, school year begins in August/September.
     * e.g., in September 2026 -> "2026 / 2027"
     */
    public static function academicYear(): string {
        $now = now();
        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');

        if ($month >= 8) {
            return $year . ' / ' . ($year + 1);
        }
        return ($year - 1) . ' / ' . $year;
    }

    /**
     * Get the Tawjihi session year according to the active calendar.
     * e.g., in September 2026, the upcoming exam session is "2027"
     */
    public static function tawjihiSession(): string {
        $now = now();
        $year = (int) $now->format('Y');
        $month = (int) $now->format('n');

        if ($month >= 8) {
            return (string) ($year + 1);
        }
        return (string) $year;
    }
}
