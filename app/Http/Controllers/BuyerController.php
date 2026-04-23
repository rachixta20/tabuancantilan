<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $stats = [
            'orders'    => $user->ordersAsBuyer()->count(),
            'delivered' => $user->ordersAsBuyer()->where('status', 'delivered')->count(),
            'pending'   => $user->ordersAsBuyer()->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
            'wishlist'  => $user->wishlists()->count(),
        ];
        $recentOrders = $user->ordersAsBuyer()->with(['seller', 'items.product'])->latest()->take(5)->get();
        return view('buyer.dashboard', compact('stats', 'recentOrders'));
    }

    public function orders()
    {
        $orders = auth()->user()->ordersAsBuyer()->with(['seller', 'items.product'])->latest()->paginate(10);
        return view('buyer.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['seller', 'buyer', 'items.product.reviews', 'statusHistories.changedBy']);
        return view('buyer.order-detail', compact('order'));
    }

    public function wishlist()
    {
        $wishlists = auth()->user()->wishlists()->with('product.seller')->latest()->paginate(12);
        return view('buyer.wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Product $product)
    {
        $existing = Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->first();
        if ($existing) {
            $existing->delete();
            $msg = 'Removed from wishlist.';
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $product->id]);
            $msg = 'Added to wishlist!';
        }
        return back()->with('success', $msg);
    }

    public function cancelOrder(Order $order)
    {
        $this->authorize('cancel', $order);
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled successfully.');
    }

    public function confirmReceipt(Order $order)
    {
        $this->authorize('confirmReceipt', $order);
        $order->update(['status' => OrderStatus::Delivered->value, 'delivered_at' => now()]);
        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => OrderStatus::Delivered->value,
            'notes'      => 'Buyer confirmed receipt.',
            'changed_by' => auth()->id(),
        ]);
        return back()->with('success', 'Order marked as received. You can now leave a review!');
    }

    public function storeReview(Request $request, Order $order)
    {
        $this->authorize('review', $order);

        if ($order->status->value !== 'delivered') {
            abort(403, 'You can only review delivered orders.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        // Verify the product is actually part of this order
        $productInOrder = $order->items()->where('product_id', $request->product_id)->exists();
        if (!$productInOrder) {
            abort(403, 'You can only review products from this order.');
        }

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $request->product_id, 'order_id' => $order->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        // Atomic update of product rating stats via single DB query
        DB::table('products')
            ->where('id', $request->product_id)
            ->update([
                'avg_rating'    => DB::raw('(SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE product_id = ' . (int)$request->product_id . ')'),
                'total_reviews' => DB::raw('(SELECT COUNT(*) FROM reviews WHERE product_id = ' . (int)$request->product_id . ')'),
            ]);

        return back()->with('success', 'Review submitted!');
    }
}
