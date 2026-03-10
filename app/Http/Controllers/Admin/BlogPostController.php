<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $posts = $query->paginate(15)->appends($request->query());
        $categories = BlogCategory::all();

        return view('admin.blog-posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog-posts.form', ['post' => null, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'content', 'blog_category_id', 'excerpt', 'meta_title', 'meta_description', 'status']);
        $data['slug'] = Str::slug($request->title);
        $data['admin_user_id'] = auth()->guard('admin')->id();

        if ($request->status === 'published' && !$request->filled('published_at')) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blog', 'public');
        }

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $count = 1;
        while (BlogPost::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $count++;
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post created.');
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogCategory::all();
        return view('admin.blog-posts.form', ['post' => $blogPost, 'categories' => $categories]);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'content', 'blog_category_id', 'excerpt', 'meta_title', 'meta_description', 'status']);

        if ($request->status === 'published' && !$blogPost->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover_image')) {
            if ($blogPost->cover_image) {
                \Storage::disk('public')->delete($blogPost->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('blog', 'public');
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->cover_image) {
            \Storage::disk('public')->delete($blogPost->cover_image);
        }
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Post deleted.');
    }
}