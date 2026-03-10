<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoadmapItem;
use App\Models\RoadmapVote;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index(Request $request)
    {
        $query = RoadmapItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $planned = (clone $query)->where('status', 'planned')->orderByDesc('votes_count')->get();
        $inProgress = (clone $query)->where('status', 'in_progress')->orderByDesc('votes_count')->get();
        $completed = (clone $query)->where('status', 'completed')->latest('updated_at')->get();

        return view('public.roadmap', compact('planned', 'inProgress', 'completed'));
    }

    public function vote(RoadmapItem $item)
    {
        $ip = request()->ip();
        $existing = RoadmapVote::where('roadmap_item_id', $item->id)->where('ip_address', $ip)->first();

        if ($existing) {
            $existing->delete();
            $item->decrement('votes_count');
            return back()->with('success', 'Vote removed.');
        }

        RoadmapVote::create([
            'roadmap_item_id' => $item->id,
            'ip_address' => $ip,
            'session_id' => session()->getId(),
        ]);

        $item->increment('votes_count');

        return back()->with('success', 'Thanks for voting!');
    }
}