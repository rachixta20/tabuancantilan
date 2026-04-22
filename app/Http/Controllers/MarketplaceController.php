<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $location = config('marketplace.location');
        $query = Product::with(['seller', 'category'])->active()->inLocation($location);

        if ($request->filled('search')) {
            $search = substr($request->search, 0, 100);
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
            );
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->filled('organic')) {
            $query->where('is_organic', true);
        }

        $sort = $request->get('sort', 'latest');
        match($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular'    => $query->orderByDesc('total_sold'),
            'rated'      => $query->orderByDesc('avg_rating'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();
        $featured   = Product::with(['seller', 'category'])->active()->featured()->inLocation($location)->take(4)->get();

        return view('marketplace.index', compact('products', 'categories', 'featured', 'sort'));
    }

    public function show(Product $product)
    {
        if (!$product->isActive()) abort(404);

        $product->load(['seller', 'category', 'reviews.user']);
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)->get();

        $inWishlist = false;
        if (auth()->check()) {
            $inWishlist = $product->wishlists()->where('user_id', auth()->id())->exists();
        }

        return view('marketplace.show', compact('product', 'related', 'inWishlist'));
    }

    public function map()
    {
        $sellers = User::where('role', 'farmer')
            ->where('account_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->withCount(['products' => fn($q) => $q->where('status', 'active')])
            ->with(['products' => fn($q) => $q->where('status', 'active')->whereNotNull('image')->latest()->take(3)])
            ->get(['id', 'name', 'farm_name', 'location', 'street', 'purok', 'barangay', 'avatar', 'bio', 'latitude', 'longitude', 'is_live', 'live_title']);

        return view('marketplace.map', compact('sellers'));
    }

    public function landing()
    {
        $location   = config('marketplace.location');
        $featured   = Product::with(['seller', 'category'])->active()->featured()->inLocation($location)->take(8)->get();
        $categories = Category::withCount(['products' => fn($q) => $q->inLocation($location)->where('status', 'active')])->take(6)->get();
        $farmers    = User::where('role', 'farmer')->where('is_active', true)->where('location', 'like', "%{$location}%")->take(6)->get();
        $stats      = [
            'farmers'  => User::where('role', 'farmer')->where('location', 'like', "%{$location}%")->count(),
            'products' => Product::active()->inLocation($location)->count(),
            'orders'   => Order::where('status', 'delivered')->count(),
            'buyers'   => User::where('role', 'buyer')->count(),
        ];
        return view('landing', compact('featured', 'categories', 'farmers', 'stats'));
    }
}
