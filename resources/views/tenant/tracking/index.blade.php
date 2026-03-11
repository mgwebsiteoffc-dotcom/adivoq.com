@extends('layouts.tenant')
@section('title', 'Tracking Keys')
@section('page_title', 'Activity Tracking')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tracking Pixels</h2>
            <p class="text-sm text-gray-500 mt-1">Monitor user activity and engagement across your website</p>
        </div>
        <a href="{{ route('dashboard.tracking.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i>New Tracking Key
        </a>
    </div>

    @if($keys->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <i class="fas fa-chart-line text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-600 font-medium">No tracking keys yet</p>
            <p class="text-sm text-gray-400 mt-1">Create a tracking key to start monitoring activity</p>
            <a href="{{ route('dashboard.tracking.create') }}" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                Create First Key
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($keys as $key)
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">{{ $key->name }}</h3>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full
                                    {{ $key->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $key->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                    {{ ucfirst($key->type) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                @if($key->brand)
                                    Brand: <span class="font-medium">{{ $key->brand->name }}</span>
                                @endif
                            </p>
                            <div class="mt-3 text-xs text-gray-500">
                                <code class="bg-gray-50 px-2 py-1 rounded font-mono">{{ $key->key }}</code>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-600">{{ $key->events()->count() }}</div>
                            <p class="text-xs text-gray-500">events</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2">
                        <a href="{{ route('dashboard.tracking.show', $key) }}" class="px-3 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg">
                            View Details
                        </a>
                        <a href="{{ route('dashboard.tracking.edit', $key) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('dashboard.tracking.destroy', $key) }}" class="inline" onsubmit="return confirm('Delete this tracking key?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
