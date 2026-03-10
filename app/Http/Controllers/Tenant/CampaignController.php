<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Brand;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with('brand')->withCount('milestones');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        $campaigns = $query->latest()->paginate(15)->appends($request->query());
        $brands = Brand::active()->orderBy('name')->get();

        return view('tenant.campaigns.index', compact('campaigns', 'brands'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $platforms = config('invoicehero.platforms');
        return view('tenant.campaigns.form', ['campaign' => null, 'brands' => $brands, 'platforms' => $platforms]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'nullable|string|max:2000',
            'platform' => 'required|in:' . implode(',', array_keys(config('invoicehero.platforms'))),
            'campaign_type' => 'required|in:sponsored_post,brand_deal,affiliate,collaboration,other',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active',
            'notes' => 'nullable|string|max:2000',
        ]);

        $campaign = Campaign::create($validated);

        return redirect()->route('dashboard.campaigns.show', $campaign)->with('success', 'Campaign created!');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['brand', 'milestones', 'invoices.payments', 'expenses']);

        $revenueCollected = $campaign->invoices->sum('amount_paid');
        $revenueProgress = $campaign->total_amount > 0
            ? min(100, round(($revenueCollected / $campaign->total_amount) * 100, 1))
            : 0;

        $completedMilestones = $campaign->milestones->where('status', 'completed')->count();
        $totalMilestones = $campaign->milestones->count();
        $totalExpenses = $campaign->expenses->sum('amount');

        return view('tenant.campaigns.show', compact(
            'campaign', 'revenueCollected', 'revenueProgress',
            'completedMilestones', 'totalMilestones', 'totalExpenses'
        ));
    }

    public function edit(Campaign $campaign)
    {
        $brands = Brand::active()->orderBy('name')->get();
        $platforms = config('invoicehero.platforms');
        return view('tenant.campaigns.form', compact('campaign', 'brands', 'platforms'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'description' => 'nullable|string|max:2000',
            'platform' => 'required|in:' . implode(',', array_keys(config('invoicehero.platforms'))),
            'campaign_type' => 'required|in:sponsored_post,brand_deal,affiliate,collaboration,other',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $campaign->update($validated);

        return redirect()->route('dashboard.campaigns.show', $campaign)->with('success', 'Campaign updated!');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->invoices()->exists()) {
            return back()->with('error', 'Cannot delete a campaign with invoices.');
        }

        $campaign->milestones()->delete();
        $campaign->forceDelete();

        return redirect()->route('dashboard.campaigns.index')->with('success', 'Campaign deleted.');
    }

    public function complete(Campaign $campaign)
    {
        $campaign->update(['status' => 'completed']);
        $campaign->milestones()->whereIn('status', ['pending', 'in_progress'])->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'Campaign marked as completed!');
    }

    public function cancel(Campaign $campaign)
    {
        $campaign->update(['status' => 'cancelled']);
        return back()->with('success', 'Campaign cancelled.');
    }
}