@extends('layouts.tenant')

@section('title', 'Create Brand')
@section('page_title', 'Create Brand')

@section('content')
<div class="max-w-xl">

    <form method="POST" action="{{ route('tenant.brands.store') }}"
          class="space-y-6 bg-surface-800 border border-surface-700 rounded-xl p-6">
        @csrf

        <div>
            <label class="block text-sm text-surface-400 mb-1">Brand Name *</label>
            <input type="text" name="name" required
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Contact Person</label>
            <input type="text" name="contact_person"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Email</label>
            <input type="email" name="email"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Phone</label>
            <input type="text" name="phone"
                class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Create
            </button>
            <a href="{{ route('tenant.brands.index') }}"
                class="px-6 py-2 text-surface-400 hover:text-white text-sm">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection