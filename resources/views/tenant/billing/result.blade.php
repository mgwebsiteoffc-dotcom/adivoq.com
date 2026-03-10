@extends('layouts.tenant')
@section('title','Billing Result')
@section('page_title','Billing')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border p-8 text-center">
        @if($status === 'success')
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900">Subscription Activated</h2>
            <p class="text-gray-600 mt-2">Your plan has been upgraded successfully.</p>
        @else
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times text-red-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-900">Payment Failed</h2>
            <p class="text-gray-600 mt-2">Please try again. You were not charged if payment failed.</p>
            @if(request('message'))
                <p class="text-sm text-gray-500 mt-3">{{ request('message') }}</p>
            @endif
        @endif

        <div class="mt-6 flex gap-3 justify-center">
            <a href="{{ route('dashboard.billing.index') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-black hover:bg-indigo-700">
                Back to Billing
            </a>
            <a href="{{ route('dashboard.home') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-sm font-black hover:bg-gray-200">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection