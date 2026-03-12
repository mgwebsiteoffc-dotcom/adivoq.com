@extends('layouts.tenant')

@section('title', 'Create Campaign')
@section('page_title', 'Create Campaign')

@section('content')
<div class="max-w-xl">

    <form method="POST"
          action="{{ route('tenant.campaigns.store') }}"
          class="space-y-6 bg-surface-800 border border-surface-700 rounded-xl p-6">
        @csrf

        <div>
            <label class="block text-sm text-surface-400 mb-1">Brand *</label>
            <select name="brand_id" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Campaign Title *</label>
            <input type="text" name="title" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Total Deal Value *</label>
            <input type="number" name="total_value" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Currency</label>
            <select name="currency"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="INR">INR</option>
                <option value="USD">USD</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Status</label>
            <select name="status"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Create
            </button>

            <a href="{{ route('tenant.campaigns.index') }}"
                class="px-6 py-2 text-surface-400 hover:text-white text-sm">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection