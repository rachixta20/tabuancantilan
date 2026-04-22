@extends('layouts.app')
@section('title', 'TABUAN — Farm Fresh Marketplace')

@section('content')

{{-- HERO --}}
<section class="hero-gradient pattern-bg relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-primary-200 text-sm font-medium px-4 py-1.5 rounded-full mb-6 border border-white/10">
                    <span class="w-2 h-2 bg-primary-400 rounded-full animate-pulse"></span>
                    100% Local & Fresh
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-6">
                    Farm to Table,<br>
                    <span class="text-primary-300">Without</span> the<br>
                    Middleman
                </h1>
                <p class="text-primary-200 text-lg leading-relaxed mb-8 max-w-lg">
                    TABUAN connects small-scale farmers and local entrepreneurs directly with consumers — fresher products, fairer prices, and stronger communities.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('marketplace') }}" class="inline-flex items-center gap-2 bg-white text-primary-700 font-semibold px-7 py-3.5 rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Shop Now
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 border border-white/30 text-white font-semibold px-7 py-3.5 rounded-xl hover:bg-white/10 transition-colors">
                        Sell Your Products →
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                @php
                    $statItems = [
                        ['value' => number_format($stats['farmers']), 'label' => 'Local Farmers', 'icon' => '🌾'],
                        ['value' => number_format($stats['products']), 'label' => 'Products Listed', 'icon' => '🥬'],
                        ['value' => number_format($stats['orders']), 'label' => 'Orders Delivered', 'icon' => '📦'],
                        ['value' => number_format($stats['buyers']), 'label' => 'Happy Buyers', 'icon' => '😊'],
                    ];
                @endphp
                @foreach($statItems as $stat)
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-6 text-white">
                        <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
                        <div class="text-3xl font-bold">{{ $stat['value'] }}</div>
                        <div class="text-primary-300 text-sm mt-1">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 60L1440 60L1440 30C1440 30 1080 0 720 0C360 0 0 30 0 30L0 60Z" fill="#f9fafb"/>
        </svg>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <h2 class="section-title">Browse by Category</h2>
        <p class="text-gray-500 mt-2">Find fresh produce, grains, livestock, and more</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('marketplace', ['category' => $category->slug]) }}"
               class="card p-5 text-center hover:shadow-md transition-shadow group cursor-pointer">
                <div class="text-4xl mb-3">{{ $category->icon ?? '🌿' }}</div>
                <p class="text-sm font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">{{ $category->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $category->products_count }} items</p>
            </a>
        @endforeach
        <a href="{{ route('marketplace') }}" class="card p-5 text-center hover:shadow-md transition-shadow border-dashed border-2 border-gray-200 flex flex-col items-center justify-center">
            <div class="text-3xl mb-2">➕</div>
            <p class="text-sm font-medium text-gray-500">View All</p>
        </a>
    </div>
</section>

{{-- FEATURED PRODUCTS --}}
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="section-title">Featured Products</h2>
                <p class="text-gray-500 mt-1">Handpicked fresh from local farms</p>
            </div>
            <a href="{{ route('marketplace') }}" class="btn-outline text-sm">View all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featured as $product)
                @include('components.product-card', ['product' => $product])
            @empty
                <div class="col-span-4 text-center py-12 text-gray-400">
                    <p>No products yet. <a href="{{ route('register') }}" class="text-primary-600">Be the first to sell!</a></p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="section-title">How TABUAN Works</h2>
        <p class="text-gray-500 mt-2">Simple, transparent, and farmer-first</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @php
            $steps = [
                ['num'=>'01','title'=>'Farmers List Products','desc'=>'Local farmers and vendors post their fresh produce with prices, stock, and location details.','icon'=>'🌱','color'=>'bg-primary-50 text-primary-600'],
                ['num'=>'02','title'=>'Buyers Browse & Order','desc'=>'Customers discover fresh local products, compare prices, and place orders directly.','icon'=>'🛒','color'=>'bg-blue-50 text-blue-600'],
                ['num'=>'03','title'=>'Direct Delivery','desc'=>'Products go straight from farm to doorstep — no middlemen, no markup, just fair trade.','icon'=>'🚚','color'=>'bg-amber-50 text-amber-600'],
            ];
        @endphp
        @foreach($steps as $step)
            <div class="card p-8 text-center">
                <div class="w-16 h-16 {{ $step['color'] }} rounded-2xl flex items-center justify-center text-3xl mx-auto mb-5">
                    {{ $step['icon'] }}
                </div>
                <span class="text-xs font-bold text-gray-400 tracking-widest uppercase">Step {{ $step['num'] }}</span>
                <h3 class="text-lg font-bold text-gray-900 mt-2 mb-3">{{ $step['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- LOCAL FARMERS --}}
@if($farmers->isNotEmpty())
<section class="bg-primary-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="section-title">Meet Our Farmers</h2>
            <p class="text-gray-500 mt-2">Dedicated growers who bring food to your table</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach($farmers as $farmer)
                <div class="card p-5 text-center hover:shadow-md transition-shadow">
                    <img src="{{ $farmer->avatar_url }}" class="w-16 h-16 rounded-xl object-cover mx-auto mb-3" alt="{{ $farmer->name }}">
                    <p class="text-sm font-semibold text-gray-800">{{ $farmer->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $farmer->location ?? 'Local Farmer' }}</p>
                    @if($farmer->is_verified)
                        <span class="badge bg-primary-100 text-primary-700 mt-2">✓ Verified</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="hero-gradient py-16 text-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Ready to Start?</h2>
        <p class="text-primary-200 text-lg mb-8">Join thousands of farmers and buyers on TABUAN today.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('register') }}" class="bg-white text-primary-700 font-semibold px-8 py-3.5 rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
                Create Free Account
            </a>
            <a href="{{ route('marketplace') }}" class="border border-white/30 text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-white/10 transition-colors">
                Explore Products
            </a>
        </div>
    </div>
</section>

@endsection
