<?php
// app/Http/Controllers/Admin/BlogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with('author');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $posts = $query->latest()->paginate(20)->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'author_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'body' => $request->body,
            'category' => $request->category,
            'status' => $request->status,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description ?: $request->excerpt,
            'published_at' => $request->status === 'published' ? now() : null,
        ];

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blog'), $filename);
            $data['cover_image'] = $filename;
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(BlogPost $blog)
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(Request $request, BlogPost $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blog->id,
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'body' => $request->body,
            'category' => $request->category,
            'status' => $request->status,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description ?: $request->excerpt,
        ];

        // Set published_at if publishing for first time
        if ($request->status === 'published' && !$blog->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($blog->cover_image && file_exists(public_path('uploads/blog/' . $blog->cover_image))) {
                unlink(public_path('uploads/blog/' . $blog->cover_image));
            }
            $file = $request->file('cover_image');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blog'), $filename);
            $data['cover_image'] = $filename;
        }

        $blog->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blog)
    {
        if ($blog->cover_image && file_exists(public_path('uploads/blog/' . $blog->cover_image))) {
            unlink(public_path('uploads/blog/' . $blog->cover_image));
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post deleted successfully.');
    }
}