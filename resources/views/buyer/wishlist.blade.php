@extends('layouts.dashboard')
@section('title', 'Wishlist')
@section('page-title', 'My Wishlist')

@section('sidebar-nav')
    @include('buyer._sidebar')
@endsection

@section('content')
@if($wishlists->isEmpty())
    <div class="card p-16 text-center">
        <div class="text-5xl mb-4">❤️</div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Your wishlist is empty</h3>
        <a href="{{ route('marketplace') }}" class="btn-primary mt-2">Explore Products</a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($wishlists as $wish)
            @include('components.product-card', ['product' => $wish->product])
        @endforeach
    </div>
    <div class="mt-6">{{ $wishlists->links() }}</div>
@endif
@endsection
