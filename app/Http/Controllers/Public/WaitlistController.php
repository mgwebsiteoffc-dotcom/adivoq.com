<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:waitlist_entries,email',
            'name' => 'nullable|string|max:255',
            'creator_type' => 'nullable|in:youtuber,instagrammer,tiktoker,blogger,podcaster,other',
        ]);

        WaitlistEntry::create([
            'email' => $request->email,
            'name' => $request->name,
            'creator_type' => $request->creator_type ?? 'other',
        ]);

        return back()->with('success', 'You\'re on the list! We\'ll notify you when your spot is ready. 🎉');
    }
}