@extends('layouts.tenant')

@section('title','Recurring Invoices')
@section('page_title','Recurring Invoices')

@section('content')
<div class="space-y-6">

<a href="{{ route('tenant.recurring.create') }}"
   class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
    + Add Recurring
</a>

<div class="bg-surface-800 border border-surface-700 rounded-xl p-6">

@foreach($recurring as $item)
<div class="flex justify-between items-center border-b border-surface-700 py-3">
    <div>
        <p class="text-white font-medium">{{ $item->title }}</p>
        <p class="text-surface-400 text-sm">
            ₹{{ number_format($item->amount,0) }} • {{ ucfirst($item->frequency) }}
        </p>
    </div>

    <div class="flex gap-3 items-center">

        <form method="POST"
              action="{{ route('tenant.recurring.toggle', $item) }}">
            @csrf
            <button class="text-sm {{ $item->active ? 'text-green-400' : 'text-red-400' }}">
                {{ $item->active ? 'Active' : 'Inactive' }}
            </button>
        </form>

        <form method="POST"
              action="{{ route('tenant.recurring.destroy', $item) }}">
            @csrf
            @method('DELETE')
            <button class="text-red-400 text-sm">
                Delete
            </button>
        </form>

    </div>
</div>
@endforeach

{{ $recurring->links() }}

</div>

</div>
@endsection