@extends('layouts.admin')

@section('title','Billing')
@section('page_title','Billing')

@section('content')
<div class="space-y-8">
    <h3 class="text-lg font-semibold text-white mb-6">Active Subscriptions</h3>
    <table class="w-full text-sm">
        <thead>
            <tr>
                <th>Tenant</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Subscription ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenants as $tenant)
            <tr>
                <td>{{ $tenant->name }}</td>
                <td>{{ $tenant->owner->email }}</td>
                <td>{{ ucfirst($tenant->plan) }}</td>
                <td>{{ ucfirst($tenant->status) }}</td>
                <td>{{ $tenant->razorpay_subscription_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection