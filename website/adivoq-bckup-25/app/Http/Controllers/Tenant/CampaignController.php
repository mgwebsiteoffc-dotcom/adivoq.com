<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Brand;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('brand')
            ->latest()
            ->paginate(20);

        return view('tenant.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        return view('tenant.campaigns.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'title' => 'required|string|max:255',
            'total_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'status' => 'required|string',
        ]);

        Campaign::create($request->all());

        return redirect()->route('tenant.campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['brand', 'milestones']);

        $stats = [
            'total_milestones' => $campaign->milestones->count(),
            'completed_milestones' => $campaign->milestones->where('status', 'completed')->count(),
        ];

        return view('tenant.campaigns.show', compact('campaign', 'stats'));
    }

    public function edit(Campaign $campaign)
    {
        $brands = Brand::orderBy('name')->get();
        return view('tenant.campaigns.edit', compact('campaign', 'brands'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'total_value' => 'required|numeric|min:0',
        ]);

        $campaign->update($request->all());

        return redirect()->route('tenant.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('tenant.campaigns.index')
            ->with('success', 'Campaign deleted.');
    }
}