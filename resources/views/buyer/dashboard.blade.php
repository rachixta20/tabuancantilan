@extends('layouts.dashboard')
@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('sidebar-nav')
    <a href="{{ route('buyer.dashboard') }}" class="sidebar-link {{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>
    <a href="{{ route('buyer.orders') }}" class="sidebar-link {{ request()->routeIs('buyer.orders*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        My Orders
    </a>
    <a href="{{ route('cart.index') }}" class="sidebar-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Cart
    </a>
    <a href="{{ route('buyer.wishlist') }}" class="sidebar-link {{ request()->routeIs('buyer.wishlist') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        Wishlist
    </a>
    <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        Messages
    </a>
    <a href="{{ route('marketplace') }}" class="sidebar-link">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        Browse Products
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        @php
            $statItems = [
                ['label'=>'Total Orders','value'=>$stats['orders'],'icon'=>'🧾','color'=>'bg-blue-50 text-blue-600'],
                ['label'=>'Delivered','value'=>$stats['delivered'],'icon'=>'✅','color'=>'bg-primary-50 text-primary-600'],
                ['label'=>'Pending','value'=>$stats['pending'],'icon'=>'⏳','color'=>'bg-amber-50 text-amber-600'],
                ['label'=>'Wishlist','value'=>$stats['wishlist'],'icon'=>'❤️','color'=>'bg-red-50 text-red-500'],
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

    <div class="card">
        <div class="flex items-center justify-between p-5 border-b border-gray-50">
            <h3 class="font-semibold text-gray-800">Recent Orders</h3>
            <a href="{{ route('buyer.orders') }}" class="text-sm text-primary-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentOrders as $order)
                <div class="px-5 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $order->seller?->avatar_url ?? '' }}" class="w-9 h-9 rounded-lg object-cover shrink-0" alt="">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">From {{ $order->seller?->name ?? '—' }} · {{ $order->items->count() }} item(s)</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</p>
                        <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize">{{ $order->status?->value }}</span>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-gray-400 text-sm">
                    No orders yet. <a href="{{ route('marketplace') }}" class="text-primary-600">Start shopping!</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
