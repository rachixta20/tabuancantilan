<div class="product-card">
    <a href="{{ route('marketplace.show', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-gray-100">
        <img src="{{ $product->image_url }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
             alt="{{ $product->name }}"
             onerror="this.src='https://placehold.co/400x400/f0fdf4/16a34a?text={{ urlencode($product->name) }}'">
        @if($product->is_organic)
            <span class="absolute top-2 left-2 badge bg-primary-100 text-primary-700">🌿 Organic</span>
        @endif
        @if($product->discount_percent)
            <span class="absolute top-2 right-2 badge bg-red-500 text-white">-{{ $product->discount_percent }}%</span>
        @endif
    </a>
    <div class="p-4 flex flex-col flex-1">
        <div class="flex items-start justify-between gap-2 mb-1">
            <a href="{{ route('marketplace.show', $product->slug) }}"
               class="text-sm font-semibold text-gray-800 hover:text-primary-600 transition-colors line-clamp-2 leading-tight">
                {{ $product->name }}
            </a>
        </div>
        <p class="text-xs text-gray-400 mb-2">{{ $product->category->name }}</p>
        @if($product->avg_rating > 0)
            <div class="flex items-center gap-1 mb-2">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3 h-3 {{ $i <= $product->avg_rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="text-xs text-gray-400 ml-0.5">({{ $product->total_reviews }})</span>
            </div>
        @endif
        <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between">
            <div>
                <span class="text-lg font-bold text-primary-700">₱{{ number_format($product->price, 2) }}</span>
                <span class="text-xs text-gray-400">/{{ $product->unit }}</span>
                @if($product->original_price)
                    <div class="text-xs text-gray-400 line-through">₱{{ number_format($product->original_price, 2) }}</div>
                @endif
            </div>
            @auth
                @if(!auth()->user()->isFarmer() && !auth()->user()->isAdmin())
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                                class="w-9 h-9 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center justify-center transition-colors active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="w-9 h-9 bg-primary-600 text-white rounded-xl hover:bg-primary-700 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            @endauth
        </div>
        <div class="flex items-center gap-1.5 mt-2">
            <img src="{{ $product->seller->avatar_url }}" class="w-4 h-4 rounded-full" alt="">
            <span class="text-xs text-gray-400 truncate">{{ $product->seller->name }}</span>
            @if($product->location)
                <span class="text-gray-300">·</span>
                <span class="text-xs text-gray-400 truncate">{{ $product->location }}</span>
            @endif
        </div>
    </div>
</div>
