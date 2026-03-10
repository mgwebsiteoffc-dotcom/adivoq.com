<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\WaitlistEntry;

class HomeController extends Controller
{
    public function index()
    {
        $latestPosts = BlogPost::published()
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get();

        $stats = [
            'creators' => \App\Models\Tenant::where('status', 'active')->count(),
            'invoices' => \App\Models\Invoice::count(),
            'revenue' => \App\Models\Payment::where('status', 'confirmed')->sum('amount'),
        ];

        return view('public.home', compact('latestPosts', 'stats'));
    }
}