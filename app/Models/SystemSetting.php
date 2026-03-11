<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Retrieve a setting by key, return default if not found.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            return $setting->value;
        }
        return $default;
    }

    /**
     * Store or update a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\Models\SystemSetting
     */
    public static function set(string $key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? (int) $value : $value]
        );
    }

    /**
     * Get all settings as key => value array.
     *
     * @return array
     */
    public static function allSettings(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
