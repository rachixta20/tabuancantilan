@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Panel')

@section('sidebar-nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Dashboard
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        Users
    </a>
    <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Products
        @if($stats['pending_products'] > 0)
            <span class="ml-auto badge bg-amber-100 text-amber-700">{{ $stats['pending_products'] }}</span>
        @endif
    </a>
    <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        Categories
    </a>
    <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Orders
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
        @php
            $statItems = [
                ['label'=>'Total Users',    'value'=>$stats['users'],   'icon'=>'👥','color'=>'bg-blue-50 text-blue-600'],
                ['label'=>'Active Farmers', 'value'=>$stats['farmers'], 'icon'=>'🌾','color'=>'bg-primary-50 text-primary-600'],
                ['label'=>'Products',       'value'=>$stats['products'],'icon'=>'📦','color'=>'bg-purple-50 text-purple-600'],
                ['label'=>'Total Orders',   'value'=>$stats['orders'],  'icon'=>'🧾','color'=>'bg-gray-50 text-gray-600'],
            ];
        @endphp
        @foreach($statItems as $s)
            <div class="stat-card">
                <div class="w-12 h-12 {{ $s['color'] }} rounded-xl flex items-center justify-center text-xl">{{ $s['icon'] }}</div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
                    <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Revenue Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card border-l-4 border-amber-400">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shrink-0">💰</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['platform_revenue'], 2) }}</p>
                <p class="text-sm text-gray-500">Platform Earnings</p>
                <p class="text-xs text-amber-600 font-medium mt-0.5">{{ $stats['commission_rate'] }}% commission on delivered orders</p>
            </div>
        </div>
        <div class="stat-card border-l-4 border-primary-400">
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center text-xl shrink-0">📊</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($stats['gmv'], 2) }}</p>
                <p class="text-sm text-gray-500">Gross Merchandise Value</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Total value of delivered orders</p>
            </div>
        </div>
        <div class="stat-card border-l-4 border-blue-400">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shrink-0">🏪</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format(max(0, $stats['gmv'] - $stats['platform_revenue']), 2) }}</p>
                <p class="text-sm text-gray-500">Seller Payouts</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Revenue distributed to sellers</p>
            </div>
        </div>
    </div>

    {{-- Alert badges --}}
    @if($stats['pending_sellers'] > 0 || $stats['pending_products'] > 0)
        <div class="flex flex-wrap gap-3 mb-6">
            @if($stats['pending_sellers'] > 0)
                <a href="{{ route('admin.users', ['status'=>'pending','role'=>'farmer']) }}"
                   class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-amber-100 transition-colors">
                    <span class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $stats['pending_sellers'] }}</span>
                    Seller applications awaiting review
                </a>
            @endif
            @if($stats['pending_products'] > 0)
                <a href="{{ route('admin.products', ['status'=>'pending']) }}"
                   class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-100 transition-colors">
                    <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $stats['pending_products'] }}</span>
                    Products awaiting approval
                </a>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Pending Seller Applications --}}
        <div class="card">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-800">Seller Applications</h3>
                    @if($stats['pending_sellers'] > 0)
                        <span class="badge bg-amber-100 text-amber-700">{{ $stats['pending_sellers'] }} pending</span>
                    @endif
                </div>
                <a href="{{ route('admin.users', ['role'=>'farmer','status'=>'pending']) }}" class="text-sm text-primary-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pendingSellers as $seller)
                    <div class="px-5 py-4 flex items-center gap-3">
                        <img src="{{ $seller->avatar_url }}" class="w-10 h-10 rounded-xl object-cover shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $seller->name }}</p>
                            <p class="text-xs text-gray-400">{{ $seller->farm_name ?? 'No farm name' }} · {{ $seller->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.users.view', $seller) }}" class="btn-primary btn-sm text-xs py-1.5 px-3">Review</a>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">No pending applications ✓</div>
                @endforelse
            </div>
        </div>

        {{-- Pending Products --}}
        <div class="card">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800">Pending Product Listings</h3>
                <a href="{{ route('admin.products', ['status' => 'pending']) }}" class="text-sm text-primary-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pendingProducts as $product)
                    <div class="px-5 py-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover"
                                 onerror="this.src='https://placehold.co/40x40/f0fdf4/16a34a?text='">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $product->seller?->name ?? '—' }} · ₱{{ number_format($product->price,2) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.products.approve', $product) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-primary btn-sm text-xs py-1.5 px-3">✓</button>
                            </form>
                            <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-danger btn-sm text-xs py-1.5 px-3">✗</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400 text-sm">All products reviewed ✓</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
