<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TrackingKey;
use App\Services\TrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected TrackingService $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * List all tracking keys
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $keys = $tenant->trackingKeys()->latest()->get();

        return view('tenant.tracking.index', compact('keys'));
    }

    /**
     * Create new tracking key
     */
    public function create()
    {
        $tenant = auth()->user()->tenant;
        $brands = $tenant->brands()->get();

        return view('tenant.tracking.create', compact('brands'));
    }

    /**
     * Store new tracking key
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'type' => 'required|in:pixel,facebook,google_ads,hotjar,custom',
            'monthly_limit' => 'nullable|integer|min:0',
        ]);

        $tenant = auth()->user()->tenant;

        $key = $this->trackingService->createKey($tenant->id, [
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'type' => $request->type,
            'monthly_limit' => $request->monthly_limit ?? 0,
        ]);

        return redirect()->route('dashboard.tracking.show', $key)
            ->with('success', 'Tracking key created successfully!');
    }

    /**
     * Show tracking key details and stats
     */
    public function show(TrackingKey $key)
    {
        $this->authorize('view', $key);

        $stats = $this->trackingService->getStats($key, 30);
        $recentEvents = $key->events()->latest()->limit(50)->get();

        return view('tenant.tracking.show', compact('key', 'stats', 'recentEvents'));
    }

    /**
     * Edit tracking key
     */
    public function edit(TrackingKey $key)
    {
        $this->authorize('update', $key);

        $tenant = auth()->user()->tenant;
        $brands = $tenant->brands()->get();

        return view('tenant.tracking.edit', compact('key', 'brands'));
    }

    /**
     * Update tracking key
     */
    public function update(Request $request, TrackingKey $key)
    {
        $this->authorize('update', $key);

        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'nullable|boolean',
            'monthly_limit' => 'nullable|integer|min:0',
        ]);

        $key->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'is_active' => $request->boolean('is_active'),
            'monthly_limit' => $request->monthly_limit ?? 0,
        ]);

        return back()->with('success', 'Tracking key updated successfully!');
    }

    /**
     * Delete tracking key
     */
    public function destroy(TrackingKey $key)
    {
        $this->authorize('delete', $key);

        $key->delete();

        return redirect()->route('dashboard.tracking.index')
            ->with('success', 'Tracking key deleted.');
    }

    /**
     * Export tracking events as CSV
     */
    public function exportEvents(TrackingKey $key)
    {
        $this->authorize('view', $key);

        $events = $key->events()->latest()->get();

        $filename = "tracking-{$key->id}-" . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($events) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Event Name', 'Session ID', 'URL', 'IP Address', 'Created At']);

            foreach ($events as $event) {
                fputcsv($file, [
                    $event->event_name,
                    $event->session_id,
                    $event->source_url,
                    $event->ip_address,
                    $event->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
