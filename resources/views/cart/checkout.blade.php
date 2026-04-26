@extends('layouts.app')
@section('title', 'Checkout — TABUAN')

@section('content')
@php $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity); @endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="section-title mb-6">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         x-data="{
             method: 'cod',
             subtotal: {{ $subtotal }},
             gcashNumber: '{{ $ewalletSettings['gcash_number'] }}',
             gcashName: '{{ $ewalletSettings['gcash_name'] }}',
             mayaNumber: '{{ $ewalletSettings['maya_number'] }}',
             mayaName: '{{ $ewalletSettings['maya_name'] }}',
             get isEwallet() { return this.method === 'gcash' || this.method === 'maya' },
             get activeNumber() { return this.method === 'gcash' ? this.gcashNumber : this.mayaNumber },
             get activeName() { return this.method === 'gcash' ? this.gcashName : this.mayaName },
         }">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('cart.place-order') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Delivery Information --}}
                <div class="card p-5" x-show="method !== 'walkin'" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-4">Delivery Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="label">Delivery Address <span class="text-red-500">*</span></label>
                            <textarea name="delivery_address" rows="3" :required="method !== 'walkin'" class="input resize-none {{ $errors->has('delivery_address') ? 'border-red-400' : '' }}"
                                      placeholder="House/Unit No., Street, Barangay, City, Province">{{ old('delivery_address', auth()->user()->location) }}</textarea>
                            @error('delivery_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label">Order Notes (optional)</label>
                            <textarea name="notes" rows="2" class="input resize-none"
                                      placeholder="Special instructions for the farmer/vendor...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Walk-in notice --}}
                <div class="card p-5 bg-primary-50 border border-primary-200" x-show="method === 'walkin'" x-transition>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🏪</span>
                        <div>
                            <p class="font-semibold text-primary-800 text-sm">Pick-up at the Store</p>
                            <p class="text-xs text-primary-600 mt-1">No delivery needed. Visit the seller's local store to pick up your order and pay in person.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="label">Order Notes (optional)</label>
                        <textarea name="notes" rows="2" class="input resize-none"
                                  placeholder="Any notes for the seller...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Payment Method --}}
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
                                    <p class="text-xs text-gray-500">Pay in cash when your order arrives</p>
                                </div>
                                <div :class="method === 'cod' ? 'border-primary-500 bg-primary-500' : 'border-gray-300'"
                                     class="ml-auto w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0">
                                    <div x-show="method === 'cod'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        {{-- GCash --}}
                        @if($ewalletSettings['gcash_number'])
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="gcash" x-model="method" class="sr-only">
                            <div :class="method === 'gcash' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                                 class="border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:border-blue-300">
                                <span class="text-2xl">📱</span>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">GCash</p>
                                    <p class="text-xs text-gray-500">Pay via GCash e-wallet</p>
                                </div>
                                <div :class="method === 'gcash' ? 'border-blue-500 bg-blue-500' : 'border-gray-300'"
                                     class="ml-auto w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0">
                                    <div x-show="method === 'gcash'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>
                        @endif

                        {{-- Maya --}}
                        @if($ewalletSettings['maya_number'])
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="maya" x-model="method" class="sr-only">
                            <div :class="method === 'maya' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                                 class="border-2 rounded-xl p-4 flex items-center gap-3 transition-all hover:border-green-300">
                                <span class="text-2xl">💚</span>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">Maya</p>
                                    <p class="text-xs text-gray-500">Pay via Maya e-wallet</p>
                                </div>
                                <div :class="method === 'maya' ? 'border-green-500 bg-green-500' : 'border-gray-300'"
                                     class="ml-auto w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0">
                                    <div x-show="method === 'maya'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>
                        @endif

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
                    </div>
                </div>

                {{-- E-wallet Payment Instructions --}}
                <div class="card p-5 border-blue-200 bg-blue-50" x-show="isEwallet" x-transition>
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <span>📲</span> How to Pay
                    </h3>
                    <ol class="text-sm text-gray-700 space-y-2 mb-4 list-decimal list-inside">
                        <li>Open your <span x-text="method === 'gcash' ? 'GCash' : 'Maya'"></span> app</li>
                        <li>Send payment to the number below</li>
                        <li>Copy the <strong>reference/transaction number</strong> from your app</li>
                        <li>Paste it in the field below, then place your order</li>
                    </ol>

                    <div class="bg-white rounded-xl border border-blue-200 p-4 mb-4 text-center">
                        <p class="text-xs text-gray-400 mb-1">Send payment to</p>
                        <p class="text-2xl font-bold text-gray-900 tracking-wide" x-text="activeNumber"></p>
                        <p class="text-sm text-gray-600 mt-1" x-text="activeName"></p>
                        <p class="text-xs font-semibold mt-2 text-primary-700"
                           x-text="'Amount: ₱' + (subtotal + 50).toLocaleString('en-PH', { minimumFractionDigits: 2 })"></p>
                    </div>

                    <div>
                        <label class="label">Reference / Transaction Number <span class="text-red-500">*</span></label>
                        <input type="text" name="payment_reference"
                               value="{{ old('payment_reference') }}"
                               :required="isEwallet"
                               maxlength="100"
                               placeholder="e.g. 1234567890123"
                               class="input font-mono tracking-wider {{ $errors->has('payment_reference') ? 'border-red-400' : '' }}">
                        @error('payment_reference')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">Found in your app's transaction history after sending</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

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

            <div class="mt-4 text-xs text-gray-400 text-center" x-show="isEwallet" x-transition>
                Your order will be confirmed after the seller verifies your payment reference.
            </div>
        </div>
    </div>
</div>
@endsection
