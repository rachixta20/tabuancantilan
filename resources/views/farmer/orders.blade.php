@extends('layouts.dashboard')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('sidebar-nav')
    @include('farmer._sidebar')
@endsection

@section('content')
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-150">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3.5 font-semibold text-gray-600">Order</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Buyer</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Items</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Total</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Payment</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <p class="font-mono font-semibold text-gray-800 text-xs">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('M d, Y') }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $order->buyer?->avatar_url ?? '' }}" class="w-7 h-7 rounded-lg" alt="">
                            <span class="text-gray-700">{{ $order->buyer?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-500">{{ $order->items->count() }} item(s)</td>
                    <td class="px-5 py-4 font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</td>
                    <td class="px-5 py-4">
                        <span class="uppercase text-xs font-semibold text-gray-600">{{ $order->payment_method?->label() }}</span>
                        @if($order->payment_reference)
                            <p class="text-xs text-gray-400 font-mono mt-0.5">Ref: {{ $order->payment_reference }}</p>
                        @endif
                        @if($order->payment_method?->isEwallet() && $order->payment_status?->value !== 'paid')
                            <span class="badge bg-amber-100 text-amber-700 mt-1">Awaiting verification</span>
                        @elseif($order->payment_method?->isEwallet() && $order->payment_status?->value === 'paid')
                            <span class="badge bg-primary-100 text-primary-700 mt-1">Payment verified</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize">
                            {{ $order->status?->value }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex flex-col gap-2">
                            {{-- Confirm payment button for unverified e-wallet orders --}}
                            @if($order->payment_method?->isEwallet() && $order->payment_status?->value !== 'paid')
                                <form action="{{ route('farmer.orders.confirm-payment', $order) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-primary text-xs py-1.5 px-3 w-full"
                                            onclick="return confirm('Confirm that you received payment for order {{ $order->order_number }}?')">
                                        ✓ Confirm Payment
                                    </button>
                                </form>
                            @endif

                            {{-- Order status update --}}
                            @if(!in_array($order->status?->value, ['shipped', 'delivered', 'cancelled']))
                                <form action="{{ route('farmer.orders.status', $order) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="status" class="input text-xs py-1.5 pr-6 w-auto">
                                        @php
                                            $nexts = ['pending'=>['confirmed','cancelled'],'confirmed'=>['processing','cancelled'],'processing'=>['shipped']];
                                        @endphp
                                        @foreach($nexts[$order->status?->value] ?? [] as $next)
                                            <option value="{{ $next }}" class="capitalize">{{ ucfirst($next) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-primary btn-sm text-xs py-1.5 px-3">Update</button>
                                </form>
                            @elseif($order->status?->value === 'shipped')
                                <span class="text-xs text-amber-600 font-medium">Waiting for buyer to confirm receipt</span>
                            @else
                                <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-3">📋</div>
                        <p>No orders yet</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
