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

    public function confirmReceipt(Request $request, Order $order)
    {
        $this->authorize('confirmReceipt', $order);

        $request->validate(['otp' => 'required|string|size:6']);

        if ($order->delivery_otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Incorrect code. Please try again.'])->withInput();
        }

        if ($order->delivery_otp_expires_at && now()->isAfter($order->delivery_otp_expires_at)) {
            return back()->withErrors(['otp' => 'This code has expired. Ask the seller to resend.'])->withInput();
        }

        $order->update([
            'status'                  => OrderStatus::Delivered->value,
            'delivered_at'            => now(),
            'delivery_otp'            => null,
            'delivery_otp_expires_at' => null,
            'payout_status'           => 'held',
            'payout_due_at'           => now()->addHours(48),
        ]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => OrderStatus::Delivered->value,
            'notes'      => 'Buyer confirmed receipt via OTP.',
            'changed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Order received! You can now leave a review. You have 48 hours to raise a dispute if there is an issue.');
    }

    public function disputeOrder(Request $request, Order $order)
    {
        abort_if(auth()->id() !== $order->buyer_id, 403);
        abort_if(!$order->canDispute(), 403, 'Dispute window has closed.');

        $request->validate(['reason' => 'required|string|max:1000']);

        $order->update(['payout_status' => 'disputed']);

        \App\Models\Report::create([
            'reporter_id'        => auth()->id(),
            'reported_user_id'   => $order->seller_id,
            'order_id'           => $order->id,
            'type'               => 'non_delivery',
            'description'        => $request->reason,
            'status'             => 'pending',
        ]);

        return back()->with('success', 'Dispute raised. Our team will review your case.');
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
