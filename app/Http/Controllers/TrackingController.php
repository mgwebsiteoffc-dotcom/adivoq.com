<?php

namespace App\Http\Controllers;

use App\Services\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Tracking pixel endpoint - responds with 1x1 GIF
     * Usage: <img src="https://yourdomain.com/track/pixel?key=tk_xxx&event=page_view&session=xyz" />
     */
    public function pixel(Request $request)
    {
        $key = $request->query('key');
        $event = $request->query('event', 'page_view');
        $sessionId = $request->query('session', Str::uuid());

        if ($key) {
            $data = [
                'source_url' => $request->query('url'),
                'custom_data' => $request->query('data'),
            ];
            $this->trackingService->track($key, $event, $data, $sessionId);
        }

        // Return 1x1 transparent GIF
        return response($this->getPixelGif(), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * JSON tracking endpoint
     * POST /track/event
     * Body: { "key": "tk_xxx", "event": "purchase", "data": {...}, "session": "xyz" }
     */
    public function event(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'event' => 'required|string',
            'data' => 'nullable|array',
            'session' => 'nullable|string',
        ]);

        $success = $this->trackingService->track(
            $request->input('key'),
            $request->input('event'),
            $request->input('data', []),
            $request->input('session')
        );

        return response()->json([
            'success' => $success,
            'session' => $request->input('session', Str::uuid()),
        ]);
    }

    /**
     * Get 1x1 transparent GIF pixel
     */
    protected function getPixelGif(): string
    {
        return base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    }
}
