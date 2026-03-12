@extends('layouts.public')

@section('title', 'HSN Code ' . $hsn->code . ' - ' . $hsn->description)
@section('meta_description', 'HSN Code ' . $hsn->code . ': ' . $hsn->description . '. Learn about this GST classification, applicable to ' . $hsn->applicable_to . '.')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('hsn.search') }}" class="hover:text-blue-600">HSN Codes</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $hsn->code }}</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                    <!-- Header -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <div class="flex items-baseline gap-4 mb-4">
                            <span class="text-5xl font-bold text-blue-600 font-mono">{{ $hsn->code }}</span>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $hsn->applicable_to === 'Goods' ? 'bg-green-100 text-green-800' : ($hsn->applicable_to === 'Service' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $hsn->applicable_to }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $hsn->description }}</h1>
                    </div>

                    <!-- Details -->
                    <div class="space-y-6">
                        @if($hsn->notes)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Additional Information</h3>
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                                    <p class="text-gray-700">{{ $hsn->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Classification Type</h3>
                            <p class="text-gray-700 mb-2">
                                This HSN code is applicable for:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                @if(in_array($hsn->applicable_to, ['Goods', 'Both']))
                                    <li>Goods classification in GST</li>
                                @endif
                                @if(in_array($hsn->applicable_to, ['Service', 'Both']))
                                    <li>Service classification in GST (SAC)</li>
                                @endif
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Usage</h3>
                            <p class="text-gray-700">
                                This HSN code should be used when:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 mt-2">
                                <li>Filing GST returns (GSTR-1, GSTR-3B)</li>
                                <li>Creating customer invoices</li>
                                <li>Classifying your products or services</li>
                                <li>Determining applicable tax rates</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Related Codes -->
                @if($relatedCodes->count() > 0)
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar HSN Codes</h2>
                        <div class="space-y-3">
                            @foreach($relatedCodes as $code)
                                <a href="{{ route('hsn.show', $code->slug) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition">
                                    <div class="flex items-start gap-4">
                                        <span class="text-xl font-bold text-blue-600 font-mono flex-shrink-0">{{ $code->code }}</span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $code->description }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $code->applicable_to === 'Goods' ? 'Goods' : ($code->applicable_to === 'Service' ? 'Service' : 'Both') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Code Details Card -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Code Details</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">HSN Code</p>
                            <p class="text-lg font-mono font-bold text-blue-600">{{ $hsn->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Type</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $hsn->applicable_to }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">First Added</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $hsn->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-700 mb-4">
                        Using this HSN code? Create invoices with proper classification.
                    </p>
                    @auth
                        <a href="{{ route('dashboard.invoices.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium text-sm">
                            Create Invoice
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition font-medium text-sm">
                            Sign In
                        </a>
                    @endauth
                </div>

                <!-- Back Link -->
                <div class="mt-6">
                    <a href="{{ route('hsn.search') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2">
                        ← Back to Search
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
