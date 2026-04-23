@extends('layouts.dashboard')
@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('sidebar-nav')
    @include('buyer._sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('buyer.orders') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary-600 mb-5">
        ← Back to Orders
    </a>

    <div class="card p-6 mb-5">
        <div class="flex items-start justify-between mb-5">
            <div>
                <p class="font-mono text-lg font-bold text-gray-800">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-400 mt-0.5">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize text-sm px-3 py-1">
                {{ $order->status?->label() ?? $order->status?->value }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-gray-400 text-xs mb-1">Seller</p>
                <div class="flex items-center gap-2">
                    <img src="{{ $order->seller->avatar_url }}" class="w-6 h-6 rounded-lg" alt="">
                    <p class="font-semibold text-gray-800">{{ $order->seller?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-gray-400 text-xs mb-1">Payment</p>
                <p class="font-semibold text-gray-800">{{ $order->payment_method?->label() ?? $order->payment_method?->value }}</p>
                <span class="badge {{ $order->payment_status?->value === 'paid' ? 'bg-primary-100 text-primary-700' : 'bg-amber-100 text-amber-700' }} mt-1">
                    {{ $order->payment_status?->label() ?? $order->payment_status?->value }}
                </span>
            </div>
        </div>

        @if($order->delivery_address)
            <div class="bg-gray-50 rounded-xl p-3 text-sm mb-5">
                <p class="text-gray-400 text-xs mb-1">Delivery Address</p>
                <p class="text-gray-800">{{ $order->delivery_address }}</p>
            </div>
        @endif

        @if($order->notes)
            <div class="bg-amber-50 rounded-xl p-3 text-sm mb-5">
                <p class="text-gray-400 text-xs mb-1">Order Notes</p>
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
        @endif

        <h3 class="font-semibold text-gray-800 mb-3">Items Ordered</h3>
        <div class="space-y-3 mb-5">
            @foreach($order->items as $item)
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                        @if($item->product)
                            <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover" alt="">
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-400">₱{{ number_format($item->price, 2) }} × {{ $item->quantity }} {{ $item->unit }}</p>
                    </div>
                    <p class="font-bold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between text-gray-600"><span>Delivery Fee</span><span>₱{{ number_format($order->delivery_fee, 2) }}</span></div>
            <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-100 mt-1">
                <span>Total</span>
                <span class="text-primary-700">₱{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Order Status Timeline --}}
    @if($order->statusHistories->isNotEmpty())
        <div class="card p-6 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4">Order Timeline</h3>
            <div class="relative">
                <div class="absolute left-3.5 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                <div class="space-y-4">
                    @foreach($order->statusHistories as $history)
                        @php
                            $colorMap = [
                                'pending'    => 'yellow',
                                'confirmed'  => 'blue',
                                'processing' => 'indigo',
                                'shipped'    => 'purple',
                                'delivered'  => 'green',
                                'cancelled'  => 'red',
                            ];
                            $color = $colorMap[$history->status] ?? 'gray';
                        @endphp
                        <div class="flex items-start gap-4 relative">
                            <div class="w-7 h-7 rounded-full bg-{{ $color }}-100 border-2 border-{{ $color }}-400 flex items-center justify-center shrink-0 z-10">
                                <div class="w-2 h-2 rounded-full bg-{{ $color }}-500"></div>
                            </div>
                            <div class="flex-1 pb-1">
                                <p class="text-sm font-semibold text-gray-800 capitalize">{{ $history->status }}</p>
                                @if($history->notes)
                                    <p class="text-xs text-gray-500">{{ $history->notes }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $history->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel Button --}}
    @if($order->canBeCancelledByBuyer())
        <div class="card p-5 mb-5" x-data="{ confirm: false }">
            <h3 class="font-semibold text-gray-800 mb-3">Cancel Order</h3>
            <p class="text-sm text-gray-500 mb-3">You can cancel this order while it is still pending or confirmed.</p>
            <template x-if="!confirm">
                <button @click="confirm = true" class="btn-secondary text-red-600 border-red-200 hover:bg-red-50 text-sm py-2 px-4">
                    Cancel This Order
                </button>
            </template>
            <template x-if="confirm">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-medium text-red-700 mb-3">Are you sure you want to cancel this order?</p>
                    <div class="flex gap-2">
                        <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700 text-sm py-2 px-4">Yes, Cancel</button>
                        </form>
                        <button @click="confirm = false" class="btn-secondary text-sm py-2 px-4">No, Keep Order</button>
                    </div>
                </div>
            </template>
        </div>
    @endif

    {{-- Report --}}
    <div class="mb-5 text-right">
        <a href="{{ route('reports.create', ['user_id' => $order->seller_id, 'order_id' => $order->id]) }}"
           class="text-xs text-gray-400 hover:text-red-500 transition-colors">Report an issue with this order</a>
    </div>

    {{-- Review Form --}}
    @if($order->status?->value === 'delivered')
        <div class="card p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Leave a Review</h3>
            @foreach($order->items as $item)
                @if($item->product)
                    <form action="{{ route('buyer.review.store', $order) }}" method="POST"
                          class="mb-4 p-4 bg-gray-50 rounded-xl"
                          x-data="{ rating: 5, submitting: false }" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                        <p class="text-sm font-medium text-gray-700 mb-3">{{ $item->product_name }}</p>
                        <div class="flex gap-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        class="text-2xl transition-transform hover:scale-110"
                                        :class="{{ $i }} <= rating ? 'text-amber-400' : 'text-gray-300'">★</button>
                            @endfor
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                        <textarea name="comment" rows="2" class="input resize-none text-sm mb-3 w-full"
                                  placeholder="Share your experience..."></textarea>
                        <button type="submit" :disabled="submitting" class="btn-primary text-sm py-2 px-5"
                                x-text="submitting ? 'Submitting...' : 'Submit Review'"></button>
                    </form>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
