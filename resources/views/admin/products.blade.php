@extends('layouts.dashboard')
@section('title', 'Products — Admin')
@section('page-title', 'Manage Products')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="flex gap-3 mb-5">
    @foreach(['','pending','active','inactive'] as $s)
        <a href="{{ route('admin.products', $s ? ['status' => $s] : []) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ request('status') === $s || (!request('status') && $s === '') ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            {{ $s ? ucfirst($s) : 'All' }}
        </a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[600px]">
        <thead>
            <tr class="bg-gray-50 text-left">
                <th class="px-5 py-3.5 font-semibold text-gray-600">Product</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Seller</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Price</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3.5 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->category?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $product->seller?->name ?? '—' }}</td>
                    <td class="px-5 py-4 font-semibold text-gray-800">₱{{ number_format($product->price,2) }}</td>
                    <td class="px-5 py-4">
                        @php
                            $colors = ['active'=>'primary','pending'=>'amber','inactive'=>'red'];
                            $statusVal = $product->status?->value ?? 'inactive';
                        @endphp
                        <span class="badge bg-{{ $colors[$statusVal] ?? 'gray' }}-100 text-{{ $colors[$statusVal] ?? 'gray' }}-700 capitalize">{{ $statusVal }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            @if($product->status?->value === 'pending')
                                <form action="{{ route('admin.products.approve', $product) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-primary btn-sm text-xs py-1.5 px-3">Approve</button>
                                </form>
                            @endif
                            @if($product->status?->value !== 'inactive')
                                <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-danger btn-sm text-xs py-1.5 px-3">Reject</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">No products found</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($products->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $products->links() }}</div>
    @endif
</div>
@endsection
