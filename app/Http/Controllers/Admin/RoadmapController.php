<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapItem;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index(Request $request)
    {
        $query = RoadmapItem::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate(20)->appends($request->query());

        return view('admin.roadmap.index', compact('items'));
    }

    public function create()
    {
        return view('admin.roadmap.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:feature,improvement,bug_fix,integration',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high',
            'target_quarter' => 'nullable|string|max:10',
        ]);

        RoadmapItem::create($request->only(['title', 'description', 'category', 'status', 'priority', 'target_quarter']));

        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item created.');
    }

    public function edit(RoadmapItem $roadmap)
    {
        return view('admin.roadmap.form', ['item' => $roadmap]);
    }

    public function update(Request $request, RoadmapItem $roadmap)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:feature,improvement,bug_fix,integration',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high',
            'target_quarter' => 'nullable|string|max:10',
        ]);

        $roadmap->update($request->only(['title', 'description', 'category', 'status', 'priority', 'target_quarter']));

        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item updated.');
    }

    public function destroy(RoadmapItem $roadmap)
    {
        $roadmap->delete();
        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item deleted.');
    }
}