<!-- resources/views/public/roadmap.blade.php -->
@extends('layouts.public')

@section('title', 'Product Roadmap — AdivoQ')
@section('meta_description', 'See what\'s coming next to AdivoQ. Our transparent product roadmap shows planned features and progress.')

@section('content')
<div class="py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-16">
            <h1 class="text-3xl sm:text-4xl font-bold text-white">Product Roadmap</h1>
            <p class="mt-4 text-surface-400 max-w-2xl mx-auto">See what we're building. Your feedback shapes our priorities.</p>
        </div>

        {{-- Status Legend --}}
        <div class="flex flex-wrap items-center justify-center gap-6 mb-12">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-400 rounded-full"></span>
                <span class="text-sm text-surface-400">Completed</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-amber-400 rounded-full"></span>
                <span class="text-sm text-surface-400">In Progress</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-surface-500 rounded-full"></span>
                <span class="text-sm text-surface-400">Planned</span>
            </div>
        </div>

        {{-- Roadmap Items --}}
        <div class="space-y-4">
            @forelse($items as $item)
            @php
                $statusColors = [
                    'completed' => 'emerald',
                    'in_progress' => 'amber',
                    'planned' => 'surface',
                    'cancelled' => 'red',
                ];
                $color = $statusColors[$item->status] ?? 'surface';

                $priorityBadge = [
                    'critical' => 'bg-red-500/10 text-red-400',
                    'high' => 'bg-amber-500/10 text-amber-400',
                    'medium' => 'bg-blue-500/10 text-blue-400',
                    'low' => 'bg-surface-500/10 text-surface-400',
                ];
            @endphp

            <div class="bg-surface-800 border border-surface-700 rounded-xl p-6 hover:border-surface-600 transition-colors">
                <div class="flex items-start gap-4">
                    {{-- Status Indicator --}}
                    <div class="flex-shrink-0 mt-1">
                        <span class="w-3 h-3 bg-{{ $color }}-400 rounded-full block"></span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-white">{{ $item->title }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $priorityBadge[$item->priority] ?? '' }}">
                                {{ ucfirst($item->priority) }}
                            </span>
                            @if($item->target_quarter)
                            <span class="text-surface-500 text-sm">{{ $item->target_quarter }}</span>
                            @endif
                        </div>
                        <p class="text-surface-400 text-sm">{{ $item->description }}</p>
                    </div>

                    {{-- Status Badge --}}
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <p class="text-surface-500">No roadmap items yet.</p>
            </div>
            @endforelse
        </div>

        {{-- Feature Request CTA --}}
        <div class="mt-16 text-center bg-surface-800 border border-surface-700 rounded-xl p-8">
            <h3 class="text-xl font-semibold text-white mb-2">Have a feature idea?</h3>
            <p class="text-surface-400 mb-6">We'd love to hear from you. Your feedback helps us build better.</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition-colors text-sm">
                Submit Feature Request
            </a>
        </div>
    </div>
</div>
@endsection