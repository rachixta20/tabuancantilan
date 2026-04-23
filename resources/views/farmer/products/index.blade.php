@extends('layouts.dashboard')
@section('title', 'My Products')
@section('page-title', 'My Products')

@section('sidebar-nav')
    @include('farmer._sidebar')
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $products->total() }} products listed</p>
        <a href="{{ route('farmer.products.create') }}" class="btn-primary">+ Add Product</a>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Product</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Category</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Price</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Stock</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3.5 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover"
                                         onerror="this.src='https://placehold.co/40x40/f0fdf4/16a34a?text={{ substr($product->name,0,1) }}'">
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    @if($product->is_organic)
                                        <span class="text-xs text-primary-600">🌿 Organic</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-5 py-4 font-semibold text-gray-800">₱{{ number_format($product->price, 2) }}/{{ $product->unit }}</td>
                        <td class="px-5 py-4">
                            <span class="{{ $product->stock < 10 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $colors = ['active'=>'primary','pending'=>'amber','inactive'=>'red'];
                                $statusVal = $product->status?->value ?? 'inactive';
                            @endphp
                            <span class="badge bg-{{ $colors[$statusVal] ?? 'gray' }}-100 text-{{ $colors[$statusVal] ?? 'gray' }}-700 capitalize">
                                {{ $statusVal }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('marketplace.show', $product->slug) }}"
                                   class="text-gray-400 hover:text-gray-600 transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('farmer.products.edit', $product) }}"
                                   class="text-blue-400 hover:text-blue-600 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('farmer.products.delete', $product) }}" method="POST"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                            <div class="text-4xl mb-3">📦</div>
                            <p class="font-medium">No products yet</p>
                            <a href="{{ route('farmer.products.create') }}" class="btn-primary mt-4 inline-flex">Add your first product</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
