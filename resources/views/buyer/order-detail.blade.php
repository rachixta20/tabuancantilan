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

    {{-- OTP Confirm Receipt --}}
    @if($order->status?->value === 'shipped')
        <div class="card p-5 mb-5 border-primary-200 bg-primary-50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Confirm Delivery</h3>
                    <p class="text-xs text-gray-500">Enter the 6-digit code sent to your email to confirm you received the order.</p>
                </div>
            </div>

            @error('otp')
                <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg">{{ $message }}</div>
            @enderror

            <form action="{{ route('buyer.orders.confirm-receipt', $order) }}" method="POST" class="flex flex-col gap-3">
                @csrf @method('PATCH')
                <div>
                    <label class="label text-xs">Delivery Confirmation Code</label>
                    <input type="text" name="otp" maxlength="6" pattern="\d{6}"
                           class="input w-40 text-center font-mono text-xl tracking-widest @error('otp') border-red-400 @enderror"
                           placeholder="000000" autocomplete="one-time-code" value="{{ old('otp') }}">
                    <p class="text-xs text-gray-400 mt-1">Check your email for the code sent when the order was shipped.</p>
                </div>
                <button type="submit" class="btn-primary text-sm py-2 px-5 w-fit">Confirm Receipt</button>
            </form>
        </div>
    @endif

    {{-- Dispute Window --}}
    @if($order->canDispute())
        <div class="card p-5 mb-5 border-amber-200 bg-amber-50" x-data="{ open: false }">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-gray-800">Issue with your order?</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        You have <strong class="text-amber-700">{{ $order->payoutHoursRemaining() }} hours</strong> left to raise a dispute.
                        After this window, the seller's payout will be released.
                    </p>
                </div>
                <button @click="open = !open" class="shrink-0 text-xs text-amber-700 font-semibold border border-amber-300 bg-white hover:bg-amber-50 px-3 py-1.5 rounded-lg transition-colors">
                    Raise Dispute
                </button>
            </div>
            <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-amber-200">
                <form action="{{ route('buyer.orders.dispute', $order) }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <div>
                        <label class="label text-xs">Describe the issue</label>
                        <textarea name="reason" rows="3" required maxlength="1000"
                                  class="input resize-none text-sm w-full"
                                  placeholder="e.g. Item received was different from what was ordered. Product was damaged..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary bg-amber-600 hover:bg-amber-700 text-sm py-2 px-5 w-fit">Submit Dispute</button>
                </form>
            </div>
        </div>
    @elseif($order->status?->value === 'delivered' && $order->payout_status === 'disputed')
        <div class="card p-4 mb-5 border-red-200 bg-red-50">
            <p class="text-sm font-semibold text-red-700">⚠ Dispute in progress</p>
            <p class="text-xs text-red-500 mt-0.5">Our team is reviewing your case. We'll get back to you soon.</p>
        </div>
    @elseif($order->status?->value === 'delivered' && $order->payout_status === 'released')
        <div class="card p-4 mb-5 border-primary-200 bg-primary-50">
            <p class="text-sm font-semibold text-primary-700">✓ Order completed</p>
            <p class="text-xs text-primary-500 mt-0.5">The dispute window has closed and the seller has been paid.</p>
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

    {{-- Reviews --}}
    @if($order->status?->value === 'delivered')
        <div class="card p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Reviews</h3>
            @foreach($order->items as $item)
                @if($item->product)
                    @php $existing = $item->product->reviews->where('order_id', $order->id)->where('user_id', auth()->id())->first(); @endphp

                    @if($existing)
                        {{-- Already reviewed — show it with seller reply --}}
                        <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm font-medium text-gray-700 mb-2">{{ $item->product_name }}</p>
                            <div class="flex gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $existing->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            @if($existing->comment)
                                <p class="text-sm text-gray-600 mb-2">{{ $existing->comment }}</p>
                            @endif
                            @if($existing->seller_reply)
                                <div class="mt-2 pl-3 border-l-2 border-primary-200 bg-primary-50 rounded-r-lg py-2 pr-3">
                                    <p class="text-xs font-semibold text-primary-700 mb-0.5">Seller replied:</p>
                                    <p class="text-sm text-gray-700">{{ $existing->seller_reply }}</p>
                                </div>
                            @endif
                            <p class="text-xs text-primary-600 mt-2 font-medium">✓ Review submitted</p>
                        </div>
                    @else
                        {{-- Review form --}}
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
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
