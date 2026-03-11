<?php

namespace App\Services;

use App\Models\TrackingCode;
use App\Models\TrackedEvent;
use Illuminate\Support\Facades\Request;

class EventTrackingService
{
    /**
     * Track an event to all enabled third-party services
     */
    public static function track($eventName, $eventCategory = null, $eventData = [])
    {
        $tenant = auth()->user()?->tenant;
        if (!$tenant) {
            return;
        }

        try {
            // Log event to database
            TrackedEvent::create([
                'tenant_id' => $tenant->id,
                'event_name' => $eventName,
                'event_category' => $eventCategory,
                'event_data' => $eventData,
                'page_url' => Request::url(),
                'referrer' => Request::header('referer'),
                'user_agent' => Request::header('user-agent'),
                'ip_address' => Request::ip(),
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Event tracking error: ' . $e->getMessage());
        }
    }

    /**
     * Get enabled tracking codes grouped by service
     */
    public static function getEnabledCodes()
    {
        return TrackingCode::getEnabled()->groupBy('service_name');
    }

    /**
     * Get specific service tracking code
     */
    public static function getCodeByService($serviceName)
    {
        return TrackingCode::getByService($serviceName);
    }

    /**
     * Generate Meta Pixel code snippet
     */
    public static function renderMetaPixel()
    {
        $pixel = TrackingCode::getMetaPixel();
        if (!$pixel || !$pixel->tracking_id) {
            return '';
        }

        return <<<HTML
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixel->tracking_id}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixel->tracking_id}&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
HTML;
    }

    /**
     * Generate Google Analytics code snippet
     */
    public static function renderGoogleAnalytics()
    {
        $ga = TrackingCode::getGoogleAnalytics();
        if (!$ga || !$ga->tracking_id) {
            return '';
        }

        return <<<HTML
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$ga->tracking_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$ga->tracking_id}');
</script>
<!-- End Google Analytics -->
HTML;
    }

    /**
     * Generate Clarity code snippet
     */
    public static function renderClarity()
    {
        $clarity = TrackingCode::getClarity();
        if (!$clarity || !$clarity->tracking_id) {
            return '';
        }

        return <<<HTML
<!-- Microsoft Clarity -->
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "{$clarity->tracking_id}");
</script>
<!-- End Microsoft Clarity -->
HTML;
    }

    /**
     * Get all custom tracking codes
     */
    public static function getCustomCodes()
    {
        return TrackingCode::getCustom();
    }

    /**
     * Render all enabled tracking codes
     */
    public static function renderAllEnabled()
    {
        $html = '';
        
        $html .= self::renderMetaPixel();
        $html .= self::renderGoogleAnalytics();
        $html .= self::renderClarity();

        foreach (self::getCustomCodes() as $custom) {
            if ($custom->code) {
                $html .= "\n" . $custom->code . "\n";
            }
        }

        return $html;
    }
}
