@extends('layouts.tenant')

@section('title', $brand->name)
@section('page_title', $brand->name)

@section('content')
<div class="space-y-6">

    {{-- Back --}}
    <a href="{{ route('tenant.brands.index') }}"
       class="text-surface-400 hover:text-white text-sm inline-flex items-center gap-2">
        ← Back to Brands
    </a>

    {{-- Brand Info Card --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">{{ $brand->name }}</h2>
                <p class="text-surface-400 text-sm mt-1">{{ $brand->email }}</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('tenant.brands.edit', $brand) }}"
                   class="px-4 py-2 bg-surface-700 text-white rounded-lg text-sm hover:bg-surface-600">
                    Edit
                </a>

                <form action="{{ route('tenant.brands.destroy', $brand) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this brand?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 text-red-400 text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mt-6 text-sm">

            <div>
                <p class="text-surface-500">Contact Person</p>
                <p class="text-white">{{ $brand->contact_person ?? '-' }}</p>
            </div>

            <div>
                <p class="text-surface-500">Phone</p>
                <p class="text-white">{{ $brand->phone ?? '-' }}</p>
            </div>

            <div>
                <p class="text-surface-500">GSTIN</p>
                <p class="text-white">{{ $brand->gstin ?? '-' }}</p>
            </div>

            <div>
                <p class="text-surface-500">PAN</p>
                <p class="text-white">{{ $brand->pan ?? '-' }}</p>
            </div>

        </div>

    </div>

</div>
@endsection