@extends('layouts.app')
@section('title', 'Checkout — TABUAN')

@section('content')
@php $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity); @endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="section-title mb-6">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         x-data="{ method: 'cod', subtotal: {{ $subtotal }} }">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('cart.place-order') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Delivery Information (hidden for walk-in) --}}
                <div class="card p-5" x-show="method !== 'walkin'" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-4">Delivery Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="label">Delivery Address <span class="text-red-500">*</span></label>
                            <textarea name="delivery_address" rows="3" :required="method !== 'walkin'" class="input resize-none"
                                      placeholder="House/Unit No., Street, Barangay, City, Province">{{ old('delivery_address', auth()->user()->location) }}</textarea>
                        </div>
                        <div>
                            <label class="label">Order Notes (optional)</label>
                            <textarea name="notes" rows="2" class="input resize-none"
                                      placeholder="Special instructions for the farmer/vendor..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Walk-in notice --}}
                <div class="card p-5 bg-primary-50 border border-primary-200" x-show="method === 'walkin'" x-transition>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🏪</span>
                        <div>
                            <p class="font-semibold text-primary-800 text-sm">Pick-up at the Store</p>
                            <p class="text-xs text-primary-600 mt-1">No delivery needed. Visit the seller's local store to pick up your order and pay in person. The seller will contact you with store details after your order is confirmed.</p>
                        </div>
                    </div>
                    {{-- Still allow notes for walk-in --}}
                    <div class="mt-4">
                        <label class="label">Order Notes (optional)</label>
                        <textarea name="notes" rows="2" class="input resize-none"
                                  placeholder="Any notes for the seller..."></textarea>
                    </div>
                </div>

                <div class="card p-5">
                    <h3 class="font-semibold text-gray-800 mb-4">Payment Method</h3>
                    <div class="space-y-3">
                        {{-- Cash on Delivery --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cod" x-model="method" class="sr-only" checked>
                            <div :class="method === 'cod' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'"
                                 class="border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:border-primary-300">
                                <span class="text-2xl">💵</span>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Cash on Delivery</p>
                                    <p class="text-xs text-gray-500">Pay when your order arrives</p>
                                </div>
                                <div :class="method === 'cod' ? 'border-primary-500 bg-primary-500' : 'border-gray-300'"
                                     class="ml-auto w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0">
                                    <div x-show="method === 'cod'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        {{-- Pay on Walk-in --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="walkin" x-model="method" class="sr-only">
                            <div :class="method === 'walkin' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'"
                                 class="border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:border-primary-300">
                                <span class="text-2xl">🏪</span>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Pay on Walk-in</p>
                                    <p class="text-xs text-gray-500">Pick up at the store — no delivery fee</p>
                                </div>
                                <div :class="method === 'walkin' ? 'border-primary-500 bg-primary-500' : 'border-gray-300'"
                                     class="ml-auto w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0">
                                    <div x-show="method === 'walkin'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        {{-- GCash (disabled) --}}
                        <div class="border-2 border-gray-100 rounded-xl p-4 flex items-center gap-3 opacity-50 cursor-not-allowed">
                            <span class="text-2xl">📱</span>
                            <div>
                                <p class="font-semibold text-gray-400 text-sm">GCash</p>
                                <p class="text-xs text-gray-400">Coming soon</p>
                            </div>
                            <span class="ml-auto text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Unavailable</span>
                        </div>

                        {{-- Bank Transfer (disabled) --}}
                        <div class="border-2 border-gray-100 rounded-xl p-4 flex items-center gap-3 opacity-50 cursor-not-allowed">
                            <span class="text-2xl">🏦</span>
                            <div>
                                <p class="font-semibold text-gray-400 text-sm">Bank Transfer</p>
                                <p class="text-xs text-gray-400">Coming soon</p>
                            </div>
                            <span class="ml-auto text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Unavailable</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-4 text-base">Place Order</button>
            </form>
        </div>

        {{-- Summary --}}
        <div class="card p-5 h-fit sticky top-20">
            <h3 class="font-semibold text-gray-800 mb-4">Order Summary</h3>
            <div class="space-y-3 mb-4">
                @foreach($cartItems as $item)
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                            <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover">
                        </div>
                        <span class="flex-1 text-gray-700 truncate">{{ $item->product->name }}</span>
                        <span class="text-gray-500 text-xs">×{{ $item->quantity }}</span>
                        <span class="font-semibold text-gray-800">₱{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="space-y-1.5 text-sm border-t border-gray-100 pt-3">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Delivery</span>
                    <span x-text="method === 'walkin' ? 'Free' : '₱50.00'">₱50.00</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-100 mt-1">
                    <span>Total</span>
                    <span class="text-primary-700"
                          x-text="'₱' + (subtotal + (method === 'walkin' ? 0 : 50)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        ₱{{ number_format($subtotal + 50, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
