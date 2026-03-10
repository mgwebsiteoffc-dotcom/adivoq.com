<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $query = Guide::with('author')->withCount('steps')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $guides = $query->paginate(15)->appends($request->query());

        return view('admin.guides.index', compact('guides'));
    }

    public function create()
    {
        return view('admin.guides.form', ['guide' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:50',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'category', 'status']);
        $data['slug'] = Str::slug($request->title);
        $data['admin_user_id'] = auth()->guard('admin')->id();

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('guides', 'public');
        }

        $baseSlug = $data['slug'];
        $count = 1;
        while (Guide::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $count++;
        }

        $guide = Guide::create($data);

        return redirect()->route('admin.guides.edit', $guide)->with('success', 'Guide created. Now add steps.');
    }

    public function edit(Guide $guide)
    {
        $guide->load('steps');
        return view('admin.guides.form', ['guide' => $guide]);
    }

    public function update(Request $request, Guide $guide)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:50',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description', 'category', 'status']);

        if ($request->status === 'published' && !$guide->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            if ($guide->cover_image) \Storage::disk('public')->delete($guide->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('guides', 'public');
        }

        $guide->update($data);

        return redirect()->route('admin.guides.index')->with('success', 'Guide updated.');
    }

    public function destroy(Guide $guide)
    {
        if ($guide->cover_image) \Storage::disk('public')->delete($guide->cover_image);
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('success', 'Guide deleted.');
    }

    public function storeStep(Request $request, Guide $guide)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $maxOrder = $guide->steps()->max('sort_order') ?? 0;

        $guide->steps()->create([
            'title' => $request->title,
            'content' => $request->content,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Step added.');
    }

    public function updateStep(Request $request, Guide $guide, GuideStep $step)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $step->update($request->only(['title', 'content', 'sort_order']));

        return back()->with('success', 'Step updated.');
    }

    public function destroyStep(Guide $guide, GuideStep $step)
    {
        $step->delete();
        return back()->with('success', 'Step deleted.');
    }
}