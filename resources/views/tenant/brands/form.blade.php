@extends('layouts.tenant')
@section('title', $brand ? 'Edit Brand' : 'Add Brand')
@section('page_title', $brand ? 'Edit Brand' : 'Add New Brand')

@section('content')
<div class="max-w-3xl">
    <a href="{{ $brand ? route('dashboard.brands.show', $brand) : route('dashboard.brands.index') }}" class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-1"></i>Back
    </a>

    <form method="POST" action="{{ $brand ? route('dashboard.brands.update', $brand) : route('dashboard.brands.store') }}" enctype="multipart/form-data">
        @csrf
        @if($brand) @method('PUT') @endif

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Brand Name *</label>
                    <input type="text" name="name" value="{{ old('name', $brand?->name) }}" required placeholder="e.g., Mamaearth"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $brand?->contact_person) }}" placeholder="Brand manager name"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $brand?->email) }}" placeholder="brand@example.com"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $brand?->phone) }}" placeholder="+91 99999 99999"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Website</label>
                    <input type="url" name="website" value="{{ old('website', $brand?->website) }}" placeholder="https://example.com"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Address</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Address Line 1</label>
                    <input type="text" name="address_line1" value="{{ old('address_line1', $brand?->address_line1) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Address Line 2</label>
                    <input type="text" name="address_line2" value="{{ old('address_line2', $brand?->address_line2) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $brand?->city) }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">State & Code <span class="text-orange-500">(important for GST)</span></label>
                    <select name="state_code" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select State</option>
                        @foreach($states as $code => $name)
                            <option value="{{ $code }}" {{ old('state_code', $brand?->state_code) == $code ? 'selected' : '' }}>{{ $code }} — {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $brand?->pincode) }}" maxlength="6"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', $brand?->country ?? 'India') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Tax Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Tax Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">GSTIN</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $brand?->gstin) }}" placeholder="22AAAAA0000A1Z5" maxlength="15"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-mono uppercase focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PAN</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $brand?->pan_number) }}" placeholder="AAAAA0000A" maxlength="10"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm font-mono uppercase focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3"><i class="fas fa-info-circle mr-1"></i>State code from GSTIN will be used to determine CGST+SGST or IGST on invoices.</p>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="3" placeholder="Internal notes about this brand..."
                      class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes', $brand?->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ $brand ? route('dashboard.brands.show', $brand) : route('dashboard.brands.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-save mr-1.5"></i>{{ $brand ? 'Update' : 'Add' }} Brand
            </button>
        </div>
    </form>
</div>
@endsection