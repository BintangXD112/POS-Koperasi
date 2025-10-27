<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        switch ($setting->type) {
            case 'boolean':
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $setting->value;
            case 'json':
                return json_decode($setting->value, true);
            default:
                return $setting->value;
        }
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update([
                'value' => $value,
                'type' => $type,
                'description' => $description
            ]);
        } else {
            static::create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'description' => $description
            ]);
        }
    }
}
