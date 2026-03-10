<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function index(Request $request)
    {
        $query = WaitlistEntry::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('email', 'like', "%{$request->search}%");
        }

        $entries = $query->paginate(25)->appends($request->query());

        $stats = [
            'total' => WaitlistEntry::count(),
            'waiting' => WaitlistEntry::where('status', 'waiting')->count(),
            'invited' => WaitlistEntry::where('status', 'invited')->count(),
            'converted' => WaitlistEntry::where('status', 'converted')->count(),
        ];

        return view('admin.waitlist.index', compact('entries', 'stats'));
    }

    public function export()
    {
        $entries = WaitlistEntry::all();

        $csv = "Name,Email,Creator Type,Status,Signed Up\n";
        foreach ($entries as $e) {
            $csv .= "\"{$e->name}\",\"{$e->email}\",\"{$e->creator_type}\",\"{$e->status}\",\"{$e->created_at}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="waitlist-' . date('Y-m-d') . '.csv"');
    }

    public function sendInvites(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:waitlist_entries,id',
        ]);

        WaitlistEntry::whereIn('id', $request->ids)
            ->where('status', 'waiting')
            ->update([
                'status' => 'invited',
                'invited_at' => now(),
            ]);

        return back()->with('success', count($request->ids) . ' invitations marked as sent.');
    }
}