@extends('layouts.tenant')

@section('title', 'Edit Campaign')
@section('page_title', 'Edit Campaign')

@section('content')
<div class="max-w-xl">

    <form method="POST"
          action="{{ route('tenant.campaigns.update', $campaign) }}"
          class="space-y-6 bg-surface-800 border border-surface-700 rounded-xl p-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-surface-400 mb-1">Campaign Title *</label>
            <input type="text" name="title"
                   value="{{ $campaign->title }}"
                   required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Total Deal Value *</label>
            <input type="number" name="total_value"
                   value="{{ $campaign->total_value }}"
                   required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Status</label>
            <select name="status"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
                <option value="draft" {{ $campaign->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ $campaign->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Update
            </button>

            <a href="{{ route('tenant.campaigns.index') }}"
                class="px-6 py-2 text-surface-400 hover:text-white text-sm">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection