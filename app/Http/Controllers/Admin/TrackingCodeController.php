<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingCode;
use Illuminate\Http\Request;

class TrackingCodeController extends Controller
{
    /**
     * Display list of tracking codes
     */
    public function index()
    {
        $codes = TrackingCode::orderBy('service_name', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.tracking-codes.index', compact('codes'));
    }

    /**
     * Show the form to create a new tracking code
     */
    public function create()
    {
        $services = [
            'meta_pixel' => 'Meta Pixel',
            'google_analytics' => 'Google Analytics (GA4)',
            'clarity' => 'Microsoft Clarity',
            'custom' => 'Custom Script',
        ];

        return view('admin.tracking-codes.create', compact('services'));
    }

    /**
     * Store a new tracking code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|in:meta_pixel,google_analytics,clarity,custom',
            'tracking_id' => 'required_unless:service_name,custom|nullable|string|max:100',
            'display_name' => 'nullable|string|max:100',
            'code' => 'required_if:service_name,custom|nullable|string|max:5000',
            'is_enabled' => 'boolean',
            'note' => 'nullable|string|max:500',
        ]);

        $validated['admin_user_id'] = auth('admin')->id();

        TrackingCode::create($validated);

        return redirect()->route('admin.tracking-codes.index')
            ->with('success', 'Tracking code added successfully');
    }

    /**
     * Show the form to edit a tracking code
     */
    public function edit(TrackingCode $trackingCode)
    {
        $services = [
            'meta_pixel' => 'Meta Pixel',
            'google_analytics' => 'Google Analytics (GA4)',
            'clarity' => 'Microsoft Clarity',
            'custom' => 'Custom Script',
        ];

        return view('admin.tracking-codes.edit', compact('trackingCode', 'services'));
    }

    /**
     * Update a tracking code
     */
    public function update(Request $request, TrackingCode $trackingCode)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|in:meta_pixel,google_analytics,clarity,custom',
            'tracking_id' => 'required_unless:service_name,custom|nullable|string|max:100',
            'display_name' => 'nullable|string|max:100',
            'code' => 'required_if:service_name,custom|nullable|string|max:5000',
            'is_enabled' => 'boolean',
            'note' => 'nullable|string|max:500',
        ]);

        $trackingCode->update($validated);

        return redirect()->route('admin.tracking-codes.index')
            ->with('success', 'Tracking code updated successfully');
    }

    /**
     * Delete a tracking code
     */
    public function destroy(TrackingCode $trackingCode)
    {
        $trackingCode->delete();

        return redirect()->route('admin.tracking-codes.index')
            ->with('success', 'Tracking code deleted successfully');
    }

    /**
     * Toggle tracking code status
     */
    public function toggle(TrackingCode $trackingCode)
    {
        $trackingCode->update([
            'is_enabled' => !$trackingCode->is_enabled
        ]);

        $status = $trackingCode->is_enabled ? 'enabled' : 'disabled';

        return redirect()->route('admin.tracking-codes.index')
            ->with('success', "Tracking code {$status} successfully");
    }

    /**
     * Get event tracking statistics
     */
    public function statistics()
    {
        $stats = [
            'total_events' => \App\Models\TrackedEvent::count(),
            'today_events' => \App\Models\TrackedEvent::whereDate('created_at', today())->count(),
            'by_service' => TrackingCode::selectRaw('service_name, COUNT(*) as count')
                ->where('is_enabled', true)
                ->groupBy('service_name')
                ->get(),
        ];

        return view('admin.tracking-codes.statistics', compact('stats'));
    }
}
