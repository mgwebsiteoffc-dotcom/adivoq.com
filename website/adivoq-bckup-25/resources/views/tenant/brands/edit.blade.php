@extends('layouts.tenant')

@section('title', 'Edit Brand')
@section('page_title', 'Edit Brand')

@section('content')
<div class="max-w-xl space-y-6">

    <a href="{{ route('tenant.brands.index') }}"
       class="text-surface-400 hover:text-white text-sm inline-flex items-center gap-2">
        ← Back to Brands
    </a>

    <form method="POST"
          action="{{ route('tenant.brands.update', $brand) }}"
          class="bg-surface-800 border border-surface-700 rounded-xl p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-surface-400 mb-1">Brand Name *</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $brand->name) }}"
                   required
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Contact Person</label>
            <input type="text"
                   name="contact_person"
                   value="{{ old('contact_person', $brand->contact_person) }}"
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $brand->email) }}"
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">Phone</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $brand->phone) }}"
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">GSTIN</label>
            <input type="text"
                   name="gstin"
                   value="{{ old('gstin', $brand->gstin) }}"
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div>
            <label class="block text-sm text-surface-400 mb-1">PAN</label>
            <input type="text"
                   name="pan"
                   value="{{ old('pan', $brand->pan) }}"
                   class="w-full bg-surface-900 border border-surface-700 rounded-lg px-4 py-2 text-white text-sm">
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                Update
            </button>

            <a href="{{ route('tenant.brands.index') }}"
               class="px-6 py-2 text-surface-400 hover:text-white text-sm">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection