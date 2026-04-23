@extends('layouts.app')
@section('title', 'Marketplace — TABUAN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="section-title">Marketplace</h1>
            <p class="text-gray-500 mt-1">{{ $products->total() }} products from Cantilan, Surigao del Sur</p>
        </div>
        @auth
            @if(auth()->user()->isFarmer())
                <a href="{{ route('farmer.products.create') }}" class="btn-primary">+ List a Product</a>
            @endif
        @endauth
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Filters Sidebar --}}
        <aside class="lg:w-60 shrink-0" x-data="{ open: false }">
            {{-- Mobile toggle --}}
            <button @click="open = !open" class="lg:hidden w-full flex items-center justify-between card px-4 py-3 mb-3 text-sm font-semibold text-gray-700">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filters
                    @if(request()->hasAny(['search','category','min_price','max_price','organic']))
                        <span class="w-2 h-2 rounded-full bg-primary-500 inline-block"></span>
                    @endif
                </span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div :class="open ? 'block' : 'hidden lg:block'">
            <div class="card p-5 lg:sticky lg:top-20">
                <h3 class="font-semibold text-gray-800 mb-4 hidden lg:block">Filters</h3>
                <form action="{{ route('marketplace') }}" method="GET" class="space-y-5">

                    {{-- Search --}}
                    <div>
                        <label class="label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="input" placeholder="Search products...">
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="label">Category</label>
                        <select name="category" class="input">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="label">Price Range (₱)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   class="input text-sm" placeholder="Min">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   class="input text-sm" placeholder="Max">
                        </div>
                    </div>

                    {{-- Location (locked) --}}
                    <div>
                        <label class="label">Location</label>
                        <div class="flex items-center gap-2 bg-primary-50 border border-primary-200 rounded-xl px-3 py-2.5">
                            <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-primary-700">Cantilan, Surigao del Sur</span>
                        </div>
                    </div>

                    {{-- Organic --}}
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="organic" value="1" {{ request('organic') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600">
                        <span class="text-sm text-gray-700">🌿 Organic only</span>
                    </label>

                    <button type="submit" class="btn-primary w-full text-sm py-2.5">Apply Filters</button>
                    <a href="{{ route('marketplace') }}" class="btn-secondary w-full text-sm py-2.5 text-center">Clear</a>
                </form>
            </div>
            </div>
        </aside>

        {{-- Products Grid --}}
        <div class="flex-1">
            {{-- Sort Bar --}}
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500">{{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</p>
                <form action="{{ route('marketplace') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label class="text-sm text-gray-600">Sort:</label>
                    <select name="sort" onchange="this.form.submit()" class="input text-sm py-1.5 pr-8 w-auto">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price ↑</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price ↓</option>
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Most Sold</option>
                        <option value="rated" {{ $sort === 'rated' ? 'selected' : '' }}>Top Rated</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="card p-16 text-center">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No products found</h3>
                    <p class="text-gray-400 text-sm">Try adjusting your filters or search terms</p>
                    <a href="{{ route('marketplace') }}" class="btn-outline mt-4">Clear all filters</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
