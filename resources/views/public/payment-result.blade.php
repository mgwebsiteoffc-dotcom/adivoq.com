@extends('layouts.public')
@section('title', 'Payment Status — ' . $invoice->invoice_number)
@section('meta_robots', 'noindex,nofollow,noarchive,nosnippet')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
            <div class="p-8 text-center">
                @if($status === 'success')
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-black text-gray-900">Payment Successful</h1>
                    <p class="text-gray-600 mt-2">Thank you! Your payment has been recorded.</p>
                @else
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times text-red-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-black text-gray-900">Payment Failed</h1>
                    <p class="text-gray-600 mt-2">Your payment could not be completed. Please try again.</p>
                @endif

                @if($message)
                    <p class="text-sm text-gray-500 mt-3">{{ $message }}</p>
                @endif

                <div class="mt-6 bg-gray-50 rounded-xl p-4 text-left">
                    <p class="text-xs text-gray-500 font-semibold uppercase">Invoice</p>
                    <p class="text-lg font-bold text-gray-900">{{ $invoice->invoice_number }}</p>
                    <p class="text-sm text-gray-600">{{ $invoice->brand->name ?? '' }}</p>
                    <p class="text-sm text-gray-600 mt-1">Amount Due: <span class="font-bold">{{ $invoice->currency_symbol }}{{ number_format($invoice->amount_due,2) }}</span></p>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('payment.link', $invoice->payment_link_token) }}"
                       class="px-6 py-3 rounded-xl text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700">
                        Back to Payment Page
                    </a>

                    <a href="{{ route('home') }}"
                       class="px-6 py-3 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Go to Website
                    </a>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center mt-6">
            Powered by <a href="{{ route('home') }}" class="text-indigo-600 hover:underline">InvoiceHero</a>
        </p>
    </div>
</section>
@endsection
