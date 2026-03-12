@extends('layouts.tenant')

@section('title','Team Revenue')
@section('page_title','Team Revenue')

@section('content')

<div class="space-y-8">

    <h3 class="text-lg font-semibold text-white">
        Revenue Splits
    </h3>

    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface-700/50 text-surface-400">
                <tr>
                    <th class="px-6 py-4 text-left">Member</th>
                    <th class="px-6 py-4">Payment</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-700">

                @foreach(\App\Models\RevenueSplit::with(['teamMember.user','payment'])->latest()->get() as $split)
                <tr>
                    <td class="px-6 py-4 text-white">
                        {{ $split->teamMember->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-surface-400">
                        ₹{{ number_format($split->payment->amount,0) }}
                    </td>
                    <td class="px-6 py-4 text-green-400">
                        ₹{{ number_format($split->amount,0) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $split->status=='paid' ? 'text-green-400' : 'text-amber-400' }}">
                            {{ ucfirst($split->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($split->status=='pending')
                        <form method="POST"
                              action="{{ route('tenant.revenue.markPaid',$split->id) }}">
                            @csrf
                            <button class="text-sm text-blue-400">
                                Mark Paid
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

</div>

@endsection