@extends('layouts.admin')
@section('title', $post ? 'Edit Post' : 'New Post')
@section('page_title', $post ? 'Edit Post' : 'Create Blog Post')

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('admin.blog-posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <form method="POST" action="{{ $post ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store') }}" enctype="multipart/form-data">
        @csrf
        @if($post) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" value="{{ old('title', $post?->title) }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 font-medium text-lg">
                        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('excerpt', $post?->excerpt) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Content *</label>
                        <textarea name="content" rows="20" required
                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('content', $post?->content) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">HTML is supported.</p>
                        @error('content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">SEO Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $post?->meta_title) }}" maxlength="70"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" rows="2" maxlength="160"
                                      class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('meta_description', $post?->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Publish</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="draft" {{ old('status', $post?->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $post?->status) === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                            <select name="blog_category_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">None</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('blog_category_id', $post?->blog_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-save mr-1"></i>{{ $post ? 'Update' : 'Publish' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Cover Image</h3>
                    @if($post?->cover_image)
                        <img src="{{ asset('storage/' . $post->cover_image) }}" class="w-full h-32 object-cover rounded-lg mb-3">
                    @endif
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection