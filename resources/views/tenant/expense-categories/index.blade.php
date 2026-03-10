@extends('layouts.tenant')
@section('title','Expense Categories')
@section('page_title','Expense Categories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">Add Category</h3>
        <form method="POST" action="{{ route('dashboard.expense-categories.store') }}" class="space-y-3">
            @csrf
            <input name="name" required placeholder="Category name"
                   class="w-full px-3 py-2.5 border rounded-lg text-sm">
            <textarea name="description" rows="2" placeholder="Optional description"
                      class="w-full px-3 py-2.5 border rounded-lg text-sm"></textarea>
            <button class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-black hover:bg-indigo-700">
                Add
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-black text-gray-900 mb-4">All Categories</h3>

        <div class="space-y-3">
            @foreach($categories as $cat)
                <div class="border rounded-lg p-4" x-data="{ edit:false }">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900">
                                {{ $cat->name }}
                                @if(!$cat->tenant_id)
                                    <span class="ml-2 text-xs font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Global</span>
                                @endif
                            </p>
                            @if($cat->description)<p class="text-xs text-gray-500 mt-1">{{ $cat->description }}</p>@endif
                        </div>

                        @if($cat->tenant_id)
                            <div class="flex gap-2">
                                <button @click="edit=true" class="text-xs font-bold text-blue-600 hover:underline">Edit</button>
                                <form method="POST" action="{{ route('dashboard.expense-categories.destroy',$cat) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-bold text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($cat->tenant_id)
                        <div x-show="edit" x-cloak class="mt-3">
                            <form method="POST" action="{{ route('dashboard.expense-categories.update',$cat) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <input name="name" value="{{ $cat->name }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm">{{ $cat->description }}</textarea>
                                <div class="flex gap-2">
                                    <button class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold">Save</button>
                                    <button type="button" @click="edit=false" class="px-3 py-1.5 bg-gray-100 rounded-lg text-xs font-bold">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
@endsection