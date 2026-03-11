<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class TrackingCode extends Model
{
    protected $fillable = [
        'admin_user_id',
        'service_name',
        'tracking_id',
        'display_name',
        'code',
        'configuration',
        'is_enabled',
        'note',
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * Get all enabled tracking codes
     */
    public static function getEnabled()
    {
        return self::where('is_enabled', true)->get();
    }

    /**
     * Get tracking code by service name
     */
    public static function getByService($serviceName)
    {
        return self::where('service_name', $serviceName)
            ->where('is_enabled', true)
            ->first();
    }

    /**
     * Get Meta Pixel code
     */
    public static function getMetaPixel()
    {
        return self::getByService('meta_pixel');
    }

    /**
     * Get Google Analytics code
     */
    public static function getGoogleAnalytics()
    {
        return self::getByService('google_analytics');
    }

    /**
     * Get Clarity code
     */
    public static function getClarity()
    {
        return self::getByService('clarity');
    }

    /**
     * Get custom tracking codes
     */
    public static function getCustom()
    {
        return self::where('service_name', 'custom')
            ->where('is_enabled', true)
            ->get();
    }
}
