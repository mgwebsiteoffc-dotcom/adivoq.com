@extends('layouts.public')
@section('title', 'Product Roadmap — InvoiceHero')

@section('content')
<section class="py-12 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block px-4 py-1.5 bg-teal-50 text-teal-700 text-sm font-semibold rounded-full border border-teal-200 mb-4">ROADMAP</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900">Product <span class="gradient-text">Roadmap</span></h1>
            <p class="mt-4 text-lg text-gray-600">See what we're building next. Vote for features you want most!</p>
        </div>

        {{-- Kanban Board --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach([
                ['title' => '🎯 Planned', 'items' => $planned, 'color' => 'amber', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200'],
                ['title' => '🚧 In Progress', 'items' => $inProgress, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200'],
                ['title' => '✅ Completed', 'items' => $completed, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200'],
            ] as $column)
                <div class="{{ $column['bg'] }} rounded-2xl p-5 border {{ $column['border'] }}">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                        {{ $column['title'] }}
                        <span class="text-sm font-normal text-gray-500 bg-white px-2.5 py-1 rounded-full">{{ $column['items']->count() }}</span>
                    </h3>

                    <div class="space-y-3">
                        @forelse($column['items'] as $item)
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium mb-2
                                            {{ $item->category === 'feature' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $item->category === 'improvement' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $item->category === 'bug_fix' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $item->category === 'integration' ? 'bg-teal-100 text-teal-700' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                                        </span>
                                        <h4 class="text-sm font-bold text-gray-900">{{ $item->title }}</h4>
                                        @if($item->description)
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                    @if($item->target_quarter)
                                        <span class="text-xs text-gray-500"><i class="far fa-calendar mr-1"></i>{{ $item->target_quarter }}</span>
                                    @else
                                        <span></span>
                                    @endif

                                    @if($item->status !== 'completed')
                                        <form method="POST" action="{{ route('roadmap.vote', $item) }}">
                                            @csrf
                                            <button type="submit" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition
                                                {{ $item->votes()->where('ip_address', request()->ip())->exists()
                                                    ? 'bg-brand-100 text-brand-700 border border-brand-200'
                                                    : 'bg-gray-100 text-gray-600 hover:bg-brand-50 hover:text-brand-600 border border-gray-200' }}">
                                                <i class="fas fa-chevron-up"></i>
                                                <span>{{ $item->votes_count }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-green-600 font-medium"><i class="fas fa-check mr-1"></i>Done</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-sm text-gray-500">
                                <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                                <p>Nothing here yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection