@extends('layouts.admin')
@section('title', $guide ? 'Edit Guide' : 'New Guide')
@section('page_title', $guide ? 'Edit Guide' : 'Create Guide')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const guideEditorConfig = {
                height: 280,
                menubar: false,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste help wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | alignleft aligncenter alignright | link image | removeformat code',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
                branding: false,
                promotion: false
            };

            window.initGuideEditor = (selector) => {
                const target = document.querySelector(selector);

                if (!target || tinymce.get(target.id)) {
                    return;
                }

                tinymce.init({
                    ...guideEditorConfig,
                    selector,
                });
            };

            window.initGuideEditor('#new-step-content');
        });
    </script>
@endpush

@section('content')
<div class="max-w-4xl">
    <a href="{{ route('admin.guides.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i>Back</a>

    {{-- Guide Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <form method="POST" action="{{ $guide ? route('admin.guides.update', $guide) : route('admin.guides.store') }}" enctype="multipart/form-data">
            @csrf
            @if($guide) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $guide?->title) }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <input type="text" name="category" value="{{ old('category', $guide?->category) }}" placeholder="e.g., GST, Invoicing" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $guide?->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="draft" {{ old('status', $guide?->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $guide?->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                </div>
            </div>
            @if($guide?->cover_image)
                <div class="mb-4">
                    <p class="block text-sm font-semibold text-gray-700 mb-2">Current Cover</p>
                    <img src="{{ asset('storage/' . $guide->cover_image) }}" alt="{{ $guide->title }}" class="w-full max-w-xs h-36 object-cover rounded-lg border border-gray-200">
                </div>
            @endif
            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-1"></i>{{ $guide ? 'Update' : 'Create' }} Guide
                </button>
            </div>
        </form>
    </div>

    {{-- Steps --}}
    @if($guide)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Guide Steps ({{ $guide->steps->count() }})</h3>

            @foreach($guide->steps as $step)
                <div class="border border-gray-200 rounded-lg p-4 mb-3" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Step {{ $step->sort_order }}: {{ $step->title }}</p>
                            <div class="text-xs text-gray-600 mt-1">{!! Str::limit(strip_tags($step->content), 150) !!}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="editing = true; $nextTick(() => window.initGuideEditor('#step-content-{{ $step->id }}'))" class="text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                            <form method="POST" action="{{ route('admin.guides.steps.destroy', [$guide, $step]) }}" onsubmit="return confirm('Delete step?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                            </form>
                        </div>
                    </div>

                    <div x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('admin.guides.steps.update', [$guide, $step]) }}">
                            @csrf @method('PUT')
                            <div class="space-y-3">
                                <input type="text" name="title" value="{{ $step->title }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                <textarea id="step-content-{{ $step->id }}" name="content">{!! $step->content !!}</textarea>
                                <input type="number" name="sort_order" value="{{ $step->sort_order }}" class="w-24 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Order">
                                <div class="flex space-x-2">
                                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg">Save</button>
                                    <button type="button" @click="editing = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded-lg">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Add Step Form --}}
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Add New Step</h4>
                <form method="POST" action="{{ route('admin.guides.steps.store', $guide) }}">
                    @csrf
                    <div class="space-y-3">
                        <input type="text" name="title" placeholder="Step title *" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <textarea id="new-step-content" name="content" required></textarea>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-1"></i>Add Step
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@endsection
