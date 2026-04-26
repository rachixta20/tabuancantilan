<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\PlaceOrderRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $cartItems = auth()->user()->carts()->with('product.seller')->get();
        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:999']);

        if (!$product->isActive()) {
            return back()->with('error', 'This product is not available.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available.');
        }

        // updateOrCreate is atomic with the unique index on (user_id, product_id)
        $cart = Cart::where('user_id', auth()->id())->where('product_id', $product->id)->first();
        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', 'Added to cart!');
    }

    public function update(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);
        $request->validate(['quantity' => 'required|integer|min:1|max:999']);
        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated.');
    }

    public function remove(Cart $cart)
    {
        $this->authorize('delete', $cart);
        $cart->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        $cartItems = auth()->user()->carts()->with('product.seller')->get();
        if ($cartItems->isEmpty()) return redirect()->route('cart.index');

        $ewalletSettings = [
            'gcash_number' => Setting::get('gcash_number', ''),
            'gcash_name'   => Setting::get('gcash_name', ''),
            'maya_number'  => Setting::get('maya_number', ''),
            'maya_name'    => Setting::get('maya_name', ''),
        ];

        return view('cart.checkout', compact('cartItems', 'ewalletSettings'));
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        $cartItems = auth()->user()->carts()->with('product.seller')->get();
        if ($cartItems->isEmpty()) return redirect()->route('cart.index');

        try {
            $this->orderService->createFromCart(auth()->user(), $cartItems, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('buyer.orders')->with('success', 'Order placed successfully!');
    }
}
