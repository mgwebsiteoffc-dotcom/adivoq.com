@extends('layouts.tenant')

@section('title','Billing')
@section('page_title','Billing')

@section('content')
<div class="glass rounded-2xl p-6 max-w-2xl mx-auto space-y-8">

    <h3 class="text-lg font-semibold text-white mb-6">Your Plan</h3>

    <div class="space-y-2">
        <p><strong>Plan:</strong> {{ ucfirst($tenant->plan) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($tenant->status) }}</p>
        <p><strong>Invoices this month:</strong> {{ $tenant->invoices()->whereMonth('issue_date', now()->month)->count() }}</p>
        <p><strong>Brands:</strong> {{ $tenant->brands()->count() }}</p>
        <p><strong>Team Members:</strong> {{ $tenant->teamMembers()->count() }}</p>
    </div>

    <div class="mt-8">
        <h4 class="text-white font-semibold mb-4">Plans</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
                <h5 class="text-lg font-bold text-white">Free</h5>
                <p class="text-surface-400 text-sm mb-2">For individuals</p>
                <p class="text-2xl font-bold text-green-400 mb-4">₹0/mo</p>
                <ul class="text-surface-400 text-sm space-y-1 mb-4">
                    <li>5 Brands</li>
                    <li>10 Invoices/month</li>
                    <li>Basic Reports</li>
                </ul>
                @if($tenant->plan == 'free')
                    <span class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Current Plan</span>
                @endif
            </div>
            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6">
                <h5 class="text-lg font-bold text-white">Pro</h5>
                <p class="text-surface-400 text-sm mb-2">For growing creators</p>
                <p class="text-2xl font-bold text-brand-400 mb-4">₹499/mo</p>
                <ul class="text-surface-400 text-sm space-y-1 mb-4">
                    <li>Unlimited Brands</li>
                    <li>Unlimited Invoices</li>
                    <li>Advanced Reports</li>
                    <li>Team Access</li>
                    <li>Priority Support</li>
                </ul>
                @if($tenant->plan == 'pro')
                    <span class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Current Plan</span>
                @else
                    <a href="{{ route('tenant.billing.upgrade') }}"
                       class="px-6 py-2 bg-brand-600 text-white rounded-lg text-sm hover:bg-brand-700">
                        Upgrade to Pro
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection