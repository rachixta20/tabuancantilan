@extends('layouts.app')
@section('title', 'Cart — TABUAN')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="section-title mb-6">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-5xl mb-4">🛒</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Your cart is empty</h3>
            <p class="text-gray-400 text-sm mb-4">Add products from local farmers</p>
            <a href="{{ route('marketplace') }}" class="btn-primary">Browse Marketplace</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($cartItems as $item)
                    <div class="card p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                                <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover"
                                     onerror="this.src='https://placehold.co/64x64/f0fdf4/16a34a?text='">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 leading-tight">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->product?->seller?->name ?? '—' }}</p>
                                <p class="text-primary-600 font-bold text-sm mt-1">₱{{ number_format($item->product->price, 2) }}/{{ $item->product->unit }}</p>
                            </div>
                            <form action="{{ route('cart.remove', $item) }}" method="POST" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                @csrf @method('PATCH')
                                <button type="button" onclick="const i=this.nextElementSibling;if(i.value>1){i.value--;i.form.submit()}"
                                        class="px-3 py-2 text-gray-500 hover:bg-gray-50 font-bold text-sm">−</button>
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                       class="w-12 text-center border-0 focus:ring-0 text-sm font-semibold"
                                       onchange="this.form.submit()">
                                <button type="button" onclick="const i=this.previousElementSibling;i.value=Math.min(parseInt(i.value)+1,{{ $item->product->stock }});i.form.submit()"
                                        class="px-3 py-2 text-gray-500 hover:bg-gray-50 font-bold text-sm">+</button>
                            </form>
                            <p class="font-bold text-gray-900">₱{{ number_format($item->product->price * $item->quantity, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="card p-5 h-fit sticky top-20">
                <h3 class="font-semibold text-gray-800 mb-4">Order Summary</h3>
                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal ({{ $cartItems->count() }} items)</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery fee</span>
                        <span>₱50.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span class="text-primary-700">₱{{ number_format($total + 50, 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('cart.checkout') }}" class="btn-primary w-full py-3 text-center">Proceed to Checkout</a>
                <a href="{{ route('marketplace') }}" class="btn-secondary w-full py-2.5 mt-2 text-center text-sm">Continue Shopping</a>
            </div>
        </div>
    @endif
</div>
@endsection
