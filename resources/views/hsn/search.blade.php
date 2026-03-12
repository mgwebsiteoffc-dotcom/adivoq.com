@extends('layouts.public')

@section('title', 'HSN/SAC Code Search | Find GST Codes for Invoices')
@section('meta_description', 'Free HSN and SAC code search tool for Indian GST. Find the correct Harmonized System Nomenclature codes for goods and services. Complete HSN database with descriptions.')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <!-- Hero Section -->
    <div class="max-w-6xl mx-auto px-4 py-16 sm:py-20">
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                Free HSN/SAC Code Search Tool
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Find the correct HSN (Harmonized System Nomenclature) and SAC codes for GST classification. Search by code, description, or category.
            </p>
        </div>

        <!-- Search Form -->
        <form method="GET" class="mb-12">
            <div class="bg-white rounded-lg shadow-lg p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search HSN Code</label>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $search }}"
                            placeholder="Enter HSN code, description, or keyword..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter By</label>
                        <select 
                            name="filter"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="goods" {{ $filter === 'goods' ? 'selected' : '' }}>Goods</option>
                            <option value="service" {{ $filter === 'service' ? 'selected' : '' }}>Services</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Search
                    </button>
                    <a href="{{ route('hsn.search') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Results -->
        @if($codes->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">HSN Code</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Description</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($codes as $code)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-bold text-blue-600 text-lg">{{ $code->code }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $code->description }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $code->applicable_to === 'Goods' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $code->applicable_to }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('hsn.show', $code->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            View Details →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $codes->links() }}
                </div>
            </div>
        @elseif($search || $filter !== 'all')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <p class="text-gray-700">
                    No HSN codes found for "<strong>{{ $search }}</strong>". 
                    <a href="{{ route('hsn.search') }}" class="text-blue-600 hover:underline">Try a new search</a>
                </p>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                <p class="text-gray-600 text-lg">
                    Use the search form above to find HSN codes, or <a href="{{ route('hsn.search', ['q' => '0101']) }}" class="text-blue-600 hover:underline">browse a sample</a>.
                </p>
            </div>
        @endif
    </div>

    <!-- Info Section -->
    <div class="bg-gray-50 border-t border-gray-200 py-16">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">About HSN Codes</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">What is HSN?</h3>
                    <p class="text-gray-600">
                        HSN (Harmonized System Nomenclature) is an international standardization system for classifying commodities. In India, it's used for GST classification.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Why Accurate?</h3>
                    <p class="text-gray-600">
                        Correct HSN classification ensures proper GST rate application, accurate returns filing, and compliance with tax regulations.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">For Services</h3>
                    <p class="text-gray-600">
                        Services use SAC (Service Accounting Code) codes instead of HSN. These are listed in the 9900-9999 range in our database.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
