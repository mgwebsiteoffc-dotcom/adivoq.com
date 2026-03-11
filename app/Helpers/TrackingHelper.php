<?php

namespace App\Helpers;

use App\Services\EventTrackingService;

class TrackingHelper
{
    /**
     * Render all enabled tracking codes for the <head> tag
     */
    public static function renderHeadScripts()
    {
        return EventTrackingService::renderAllEnabled();
    }

    /**
     * Track an event from the frontend
     */
    public static function trackEvent($eventName, $eventCategory = null, $eventData = [])
    {
        EventTrackingService::track($eventName, $eventCategory, $eventData);
    }

    /**
     * Get Meta Pixel script only
     */
    public static function metaPixelScript()
    {
        return EventTrackingService::renderMetaPixel();
    }

    /**
     * Get Google Analytics script only
     */
    public static function googleAnalyticsScript()
    {
        return EventTrackingService::renderGoogleAnalytics();
    }

    /**
     * Get Clarity script only
     */
    public static function clarityScript()
    {
        return EventTrackingService::renderClarity();
    }
}
