@extends('layouts.tenant')

@section('title','Team Member Profile')
@section('page_title','Team Member Profile')

@section('content')
<div class="glass rounded-2xl p-6 space-y-8">

    <div>
        <h3 class="text-lg font-semibold text-white mb-2">Profile</h3>
        <p><strong>Name:</strong> {{ $member->user->name }}</p>
        <p><strong>Email:</strong> {{ $member->user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($member->role) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($member->status) }}</p>
    </div>

    <div>
        <h4 class="text-white font-semibold mb-2">Revenue Splits</h4>
        <ul>
            @forelse($splits as $split)
                <li>
                    Invoice: {{ $split->payment->invoice->invoice_number ?? '-' }},
                    Amount: ₹{{ number_format($split->amount,0) }},
                    Status: {{ ucfirst($split->status) }}
                </li>
            @empty
                <li>No revenue splits yet.</li>
            @endforelse
        </ul>
    </div>

    <div>
        <h4 class="text-white font-semibold mb-2">Recent Activity</h4>
        <ul>
            @forelse($activity as $log)
                <li>
                    {{ $log->action }} — {{ $log->description }} ({{ $log->created_at->diffForHumans() }})
                </li>
            @empty
                <li>No activity yet.</li>
            @endforelse
        </ul>
    </div>

</div>
@endsection