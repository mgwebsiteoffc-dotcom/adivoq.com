@extends('layouts.public')
@section('title', 'Access Denied')

@section('content')
<section class="py-16">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center shadow-sm">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-red-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-900">Access Denied (403)</h1>
            <p class="text-gray-600 mt-2">{{ $message ?? 'You do not have permission to access this page.' }}</p>

            @if(!empty($required_roles) && !empty($current_role))
                <div class="mt-4 text-sm text-gray-500">
                    <p><b>Your role:</b> <span class="capitalize">{{ $current_role }}</span></p>
                    <p><b>Required:</b> {{ implode(', ', array_map('ucfirst', $required_roles)) }}</p>
                </div>
            @endif

            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('dashboard.home') }}"
                   class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-black hover:bg-indigo-700">
                    Go to Dashboard
                </a>
                <a href="{{ url()->previous() }}"
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-sm font-black hover:bg-gray-200">
                    Go Back
                </a>
            </div>
        </div>
    </div>
</section>
@endsection