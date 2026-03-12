<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $campaign->milestones()->create([
            'title' => $request->title,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Milestone added.');
    }

    public function update(Request $request, Campaign $campaign, Milestone $milestone)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $milestone->update($request->all());

        return back()->with('success', 'Milestone updated.');
    }

    public function destroy(Campaign $campaign, Milestone $milestone)
    {
        $milestone->delete();

        return back()->with('success', 'Milestone deleted.');
    }

    public function complete(Campaign $campaign, Milestone $milestone)
    {
        $milestone->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Milestone marked completed.');
    }
}