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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $maxOrder = $campaign->milestones()->max('sort_order') ?? 0;

        $campaign->milestones()->create([
            'tenant_id' => auth()->user()->tenant_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'] ?? 0,
            'due_date' => $validated['due_date'] ?? null,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Milestone added!');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $milestone->update($validated);

        return back()->with('success', 'Milestone updated!');
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return back()->with('success', 'Milestone deleted.');
    }

    public function complete(Milestone $milestone)
    {
        $milestone->markComplete();
        return back()->with('success', 'Milestone marked as complete! 🎉');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'milestones' => 'required|array',
            'milestones.*.id' => 'required|exists:milestones,id',
            'milestones.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->milestones as $item) {
            Milestone::where('id', $item['id'])
                ->where('tenant_id', auth()->user()->tenant_id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}