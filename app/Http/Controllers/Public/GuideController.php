<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $query = Guide::published()->with('author')->latest('published_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $guides = $query->paginate(12);
        $categories = Guide::published()->whereNotNull('category')->distinct()->pluck('category');

        return view('public.guides.index', compact('guides', 'categories'));
    }

    public function show(string $slug)
    {
        $guide = Guide::published()->where('slug', $slug)->with(['steps', 'author'])->firstOrFail();

        return view('public.guides.show', compact('guide'));
    }
}