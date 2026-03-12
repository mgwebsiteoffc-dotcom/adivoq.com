<!-- resources/views/admin/blog/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Create Blog Post')
@section('page_title', 'Create Blog Post')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center gap-2 text-surface-400 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Blog
        </a>
    </div>

    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-surface-300 mb-1.5">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm"
                    placeholder="Enter post title">
                @error('title')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-surface-300 mb-1.5">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm"
                    placeholder="auto-generated-from-title">
                <p class="text-surface-500 text-xs mt-1">Leave empty to auto-generate from title</p>
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-surface-300 mb-1.5">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="2"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm resize-none"
                    placeholder="Brief summary of the post">{{ old('excerpt') }}</textarea>
            </div>

            <div>
                <label for="body" class="block text-sm font-medium text-surface-300 mb-1.5">Content *</label>
                <textarea id="body" name="body" rows="15" required
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm font-mono"
                    placeholder="Write your post content here... (HTML is supported)">{{ old('body') }}</textarea>
                <p class="text-surface-500 text-xs mt-1">HTML tags are supported</p>
                @error('body')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-surface-300 mb-1.5">Category *</label>
                    <select id="category" name="category" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="tax-tips" {{ old('category') === 'tax-tips' ? 'selected' : '' }}>Tax Tips</option>
                        <option value="guides" {{ old('category') === 'guides' ? 'selected' : '' }}>Guides</option>
                        <option value="news" {{ old('category') === 'news' ? 'selected' : '' }}>News</option>
                        <option value="tutorials" {{ old('category') === 'tutorials' ? 'selected' : '' }}>Tutorials</option>
                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-surface-300 mb-1.5">Status *</label>
                    <select id="status" name="status" required
                        class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="cover_image" class="block text-sm font-medium text-surface-300 mb-1.5">Cover Image</label>
                <input type="file" id="cover_image" name="cover_image" accept="image/*"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 focus:border-brand-500 text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-brand-600 file:text-white file:text-sm">
                <p class="text-surface-500 text-xs mt-1">Max 2MB. JPG, PNG, or WebP.</p>
            </div>
        </div>

        {{-- SEO --}}
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-4">
            <h3 class="text-lg font-semibold text-white">SEO Settings</h3>

            <div>
                <label for="meta_title" class="block text-sm font-medium text-surface-300 mb-1.5">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm"
                    placeholder="SEO title (defaults to post title)">
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-surface-300 mb-1.5">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="2"
                    class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2.5 text-surface-100 placeholder-surface-500 focus:border-brand-500 text-sm resize-none"
                    placeholder="SEO description (defaults to excerpt)">{{ old('meta_description') }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                Create Post
            </button>
            <a href="{{ route('admin.blog.index') }}" class="px-6 py-2.5 text-surface-400 hover:text-white transition-colors text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection