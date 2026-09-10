<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['key', 'value'];
    protected static array $cache = [];
    protected static bool $dbAvailable = true;

    public static function get($key, $default = null) {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key] ?? $default;
        }

        if (!static::$dbAvailable) {
            return $default;
        }

        try {
            $setting = self::where('key', $key)->first();
            static::$cache[$key] = $setting ? $setting->value : $default;
            return static::$cache[$key] ?? $default;
        } catch (\Throwable $e) {
            static::$dbAvailable = false;
            return $default;
        }
    }

    public static function set($key, $value) {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
