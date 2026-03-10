@extends('layouts.admin')
@section('title', $category ? 'Edit Category' : 'Add Category')
@section('page_title', $category ? 'Edit Category' : 'Add Blog Category')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.blog-categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ $category ? route('admin.blog-categories.update', $category) : route('admin.blog-categories.store') }}">
            @csrf
            @if($category) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $category?->name) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $category?->description) }}</textarea>
                </div>
                <div class="pt-4 border-t border-gray-100 text-right">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        {{ $category ? 'Update' : 'Create' }} Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection