@extends('layouts.app')
@section('title', $product->name . ' — TABUAN')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-600">Home</a>
        <span>/</span>
        <a href="{{ route('marketplace') }}" class="hover:text-primary-600">Marketplace</a>
        <span>/</span>
        <span class="text-gray-800">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
        {{-- Image --}}
        <div>
            <div class="card overflow-hidden aspect-square">
                <img src="{{ $product->image_url }}"
                     class="w-full h-full object-cover"
                     alt="{{ $product->name }}"
                     onerror="this.src=this.dataset.fallback"
                     data-fallback="https://placehold.co/600x600/f0fdf4/16a34a?text=Product">
            </div>
        </div>

        {{-- Details --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="badge bg-primary-100 text-primary-700">{{ $product->category->name }}</span>
                @if($product->is_organic)
                    <span class="badge bg-green-100 text-green-700">🌿 Organic</span>
                @endif
                @if($product->stock < 10)
                    <span class="badge bg-amber-100 text-amber-700">Low Stock</span>
                @endif
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>

            @if($product->avg_rating > 0)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $product->avg_rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($product->avg_rating, 1) }}</span>
                    <span class="text-sm text-gray-400">({{ $product->total_reviews }} reviews)</span>
                </div>
            @endif

            <div class="flex items-end gap-3 mb-6">
                <span class="text-4xl font-extrabold text-primary-700">₱{{ number_format($product->price, 2) }}</span>
                <span class="text-gray-400 mb-1">per {{ $product->unit }}</span>
                @if($product->original_price)
                    <span class="text-lg text-gray-400 line-through mb-1">₱{{ number_format($product->original_price, 2) }}</span>
                @endif
            </div>

            <p class="text-gray-600 leading-relaxed mb-6">{{ $product->description }}</p>

            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-gray-400 text-xs">Available Stock</p>
                    <p class="font-semibold text-gray-800 mt-0.5">{{ number_format($product->stock) }} {{ $product->unit }}</p>
                </div>
                @if($product->location)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-gray-400 text-xs">Farm Location</p>
                        <p class="font-semibold text-gray-800 mt-0.5">{{ $product->location }}</p>
                    </div>
                @endif
            </div>

            {{-- Harvest & Freshness --}}
            @if($product->harvest_date)
                @php $f = $product->freshness; $daysLeft = $product->days_until_expiry; @endphp
                <div class="mb-6 rounded-xl border {{ $f ? 'border-'.$f['color'].'-200 bg-'.$f['color'].'-50' : 'border-gray-200 bg-gray-50' }} p-4">
                    <div class="flex items-center gap-3">
                        <div class="text-2xl">{{ $f['icon'] ?? '📅' }}</div>
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-gray-800">
                                {{ $f['label'] ?? 'Harvest Info' }}
                                @if($f && $f['color'] !== 'gray')
                                    <span class="ml-2 badge bg-{{ $f['color'] }}-100 text-{{ $f['color'] }}-700">{{ $f['label'] }}</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Harvested on <strong>{{ $product->harvest_date->format('F d, Y') }}</strong>
                                ({{ $product->days_harvested === 0 ? 'today' : ($product->days_harvested === 1 ? 'yesterday' : $product->days_harvested . ' days ago') }})
                            </p>
                            @if($daysLeft !== null)
                                @if($daysLeft > 0)
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Best consumed within <strong>{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }}</strong>
                                        (by {{ $product->harvest_date->addDays($product->shelf_life_days)->format('M d, Y') }})
                                    </p>
                                @else
                                    <p class="text-xs text-red-500 mt-0.5 font-medium">Past recommended freshness date</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @auth
                @if(!auth()->user()->isFarmer() && !auth()->user()->isAdmin())
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex gap-3 mb-4">
                        @csrf
                        <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden">
                            <button type="button" onclick="decQty()" class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold">−</button>
                            <input type="number" name="quantity" id="qty" value="1" min="1" max="{{ $product->stock }}"
                                   class="w-16 text-center border-0 focus:ring-0 text-sm font-semibold">
                            <button type="button" onclick="incQty()" class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold">+</button>
                        </div>
                        <button type="submit" class="btn-primary flex-1 py-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Add to Cart
                        </button>
                    </form>

                    <div class="flex gap-3">
                        <form action="{{ route('buyer.wishlist.toggle', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="btn-secondary w-full py-3">
                                {{ $inWishlist ? '❤️ In Wishlist' : '🤍 Add to Wishlist' }}
                            </button>
                        </form>
                        @if(auth()->user()->id !== $product->user_id)
                            <form action="{{ route('messages.start', $product) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="btn-outline w-full py-3">
                                    💬 Message Seller
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-primary w-full py-3 text-center">Login to Purchase</a>
            @endauth

            {{-- Seller Card --}}
            <div class="card mt-6 p-4 flex items-center gap-4">
                <img src="{{ $product->seller->avatar_url }}" class="w-12 h-12 rounded-xl object-cover shrink-0" alt="">
                <div class="flex-1">
                    <p class="text-xs text-gray-400">Sold by</p>
                    <p class="font-semibold text-gray-800">{{ $product->seller->name }}</p>
                    @if($product->seller->location)
                        <p class="text-xs text-gray-500">📍 {{ $product->seller->location }}</p>
                    @endif
                    @php $sellerRating = round($product->seller->averageSellerRating(), 1); @endphp
                    @if($sellerRating > 0)
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-amber-400 text-xs">★</span>
                            <span class="text-xs font-medium text-gray-600">{{ number_format($sellerRating, 1) }} seller rating</span>
                        </div>
                    @endif
                </div>
                @if($product->seller->is_verified)
                    <span class="badge bg-primary-100 text-primary-700 ml-auto">✓ Verified</span>
                @endif
            </div>

            {{-- Seller policies --}}
            @if($product->seller->free_delivery || $product->seller->minimum_order)
                <div class="mt-3 flex flex-wrap gap-2">
                    @if($product->seller->free_delivery)
                        <span class="inline-flex items-center gap-1 text-xs bg-primary-50 text-primary-700 border border-primary-200 px-2.5 py-1 rounded-lg font-medium">
                            🚚 Free Delivery
                        </span>
                    @endif
                    @if($product->seller->minimum_order)
                        <span class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg font-medium">
                            🛒 Min. order ₱{{ number_format($product->seller->minimum_order, 2) }}
                        </span>
                    @endif
                </div>
            @endif

            @auth
                <div class="mt-3 text-right">
                    <a href="{{ route('reports.create', ['product_id' => $product->id, 'user_id' => $product->seller_id]) }}"
                       class="text-xs text-gray-400 hover:text-red-500 transition-colors">Report this listing</a>
                </div>
            @endauth
        </div>
    </div>

    {{-- Reviews --}}
    @if($product->reviews->isNotEmpty())
        <div class="mb-12">
            <h2 class="section-title mb-6">Customer Reviews</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($product->reviews as $review)
                    <div class="card p-5">
                        {{-- Buyer review --}}
                        <div class="flex items-start gap-3">
                            <img src="{{ $review->user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover shrink-0" alt="">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-sm text-gray-800">{{ $review->user->name }}</p>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex mt-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @if($review->comment)
                                    <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Seller reply --}}
                        @if($review->seller_reply)
                            <div class="mt-3 ml-4 pl-4 border-l-2 border-primary-200 bg-primary-50 rounded-r-xl py-3 pr-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <img src="{{ $product->seller->avatar_url }}" class="w-6 h-6 rounded-lg object-cover shrink-0" alt="">
                                    <span class="text-xs font-semibold text-primary-700">{{ $product->seller->farm_name ?: $product->seller->name }}</span>
                                    <span class="text-xs text-gray-400">· {{ $review->seller_reply_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $review->seller_reply }}</p>
                            </div>
                        @endif

                        {{-- Seller reply form (only visible to the product owner) --}}
                        @auth
                            @if(auth()->id() === $product->user_id && !$review->seller_reply)
                                <div class="mt-3 ml-4" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="text-xs text-primary-600 hover:text-primary-700 font-medium transition-colors">
                                        + Reply to this review
                                    </button>
                                    <form x-show="open" x-cloak
                                          action="{{ route('farmer.reviews.reply', $review) }}" method="POST"
                                          class="mt-2 flex flex-col gap-2">
                                        @csrf
                                        <textarea name="reply" rows="2" required maxlength="1000"
                                                  class="input text-sm resize-none"
                                                  placeholder="Write your reply..."></textarea>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn-primary text-xs py-1.5 px-4">Post Reply</button>
                                            <button type="button" @click="open = false" class="btn-outline text-xs py-1.5 px-3">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($related->isNotEmpty())
        <div>
            <h2 class="section-title mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($related as $relProduct)
                    @include('components.product-card', ['product' => $relProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function incQty() {
        const q = document.getElementById('qty');
        q.value = Math.min(parseInt(q.value) + 1, parseInt(q.max));
    }
    function decQty() {
        const q = document.getElementById('qty');
        q.value = Math.max(parseInt(q.value) - 1, 1);
    }
</script>
@endpush
@endsection
