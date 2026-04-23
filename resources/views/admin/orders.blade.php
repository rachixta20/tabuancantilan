@extends('layouts.dashboard')
@section('title', 'Orders — Admin')
@section('page-title', 'All Orders')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3.5 font-semibold text-gray-600">Order #</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Buyer</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Seller</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Total</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Payment</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $order->buyer?->name ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $order->seller?->name ?? '—' }}</td>
                    <td class="px-5 py-4 font-bold text-gray-900">₱{{ number_format($order->total,2) }}</td>
                    <td class="px-5 py-4 uppercase text-xs text-gray-500">{{ $order->payment_method?->value }}</td>
                    <td class="px-5 py-4">
                        <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize">{{ $order->status?->value }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">No orders yet</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
