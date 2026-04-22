@extends('layouts.dashboard')
@section('title', 'My Orders')
@section('page-title', 'My Orders')

@section('sidebar-nav')
    @include('buyer._sidebar')
@endsection

@section('content')
<div class="space-y-4">
    @forelse($orders as $order)
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="font-mono text-sm font-bold text-gray-800">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('F d, Y · h:i A') }} · From {{ $order->seller->name }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0 flex-wrap">
                    <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize text-xs px-3 py-1">
                        {{ $order->status }}
                    </span>
                    <a href="{{ route('buyer.order-detail', $order) }}" class="btn-secondary btn-sm text-xs">View Details</a>
                    @if(in_array($order->status, ['pending', 'confirmed']))
                        <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST"
                              onsubmit="return confirm('Cancel this order?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-danger btn-sm text-xs py-1 px-3">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="space-y-2 mb-4">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            @if($item->product)
                                <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover"
                                     onerror="this.src='https://placehold.co/32x32/f0fdf4/16a34a?text='">
                            @endif
                        </div>
                        <span class="text-sm text-gray-700 flex-1">{{ $item->product_name }}</span>
                        <span class="text-xs text-gray-400">{{ $item->quantity }} {{ $item->unit }}</span>
                        <span class="text-sm font-semibold text-gray-800">₱{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                <span class="text-sm text-gray-500">Total · {{ strtoupper($order->payment_method) }}</span>
                <span class="text-lg font-bold text-primary-700">₱{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    @empty
        <div class="card p-16 text-center">
            <div class="text-5xl mb-4">🛒</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No orders yet</h3>
            <p class="text-gray-400 text-sm mb-4">Start shopping from local farmers!</p>
            <a href="{{ route('marketplace') }}" class="btn-primary">Browse Marketplace</a>
        </div>
    @endforelse
    @if($orders->hasPages())
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
