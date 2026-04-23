<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\Farmer\StoreProductRequest;
use App\Http\Requests\Farmer\UpdateProductRequest;
use App\Mail\DeliveryOtpMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FarmerController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user->isApproved()) {
            return view('farmer.pending', ['user' => $user]);
        }
        $deliveredOrders = $user->ordersAsSeller()->where('status', 'delivered');
        $stats = [
            'products'        => $user->products()->count(),
            'orders'          => $user->ordersAsSeller()->count(),
            'earnings'        => $deliveredOrders->where('payout_status', 'released')->sum('seller_payout'),
            'held_earnings'   => $deliveredOrders->where('payout_status', 'held')->sum('seller_payout'),
            'commission_paid' => $deliveredOrders->sum('platform_fee'),
            'pending'         => $user->ordersAsSeller()->where('status', 'pending')->count(),
        ];
        $recentOrders = $user->ordersAsSeller()->with(['buyer', 'items.product'])->latest()->take(5)->get();
        $topProducts  = $user->products()->active()->orderByDesc('total_sold')->take(5)->get();
        return view('farmer.dashboard', compact('stats', 'recentOrders', 'topProducts'));
    }

    public function products()
    {
        $products = auth()->user()->products()->with('category')->latest()->paginate(10);
        return view('farmer.products.index', compact('products'));
    }

    public function createProduct()
    {
        if (!auth()->user()->canSellProducts()) {
            return redirect()->route('farmer.dashboard')
                ->with('error', 'Your account must be approved before listing products.');
        }
        $categories = Category::all();
        return view('farmer.products.create', compact('categories'));
    }

    public function storeProduct(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['user_id']    = auth()->id();
        $data['slug']       = Str::slug($data['name']) . '-' . Str::random(8);
        $data['status']     = 'active';
        $data['is_organic'] = $request->boolean('is_organic');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('farmer.products')->with('success', 'Product listed successfully!');
    }

    public function editProduct(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::all();
        return view('farmer.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['is_organic'] = $request->boolean('is_organic');

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('farmer.products')->with('success', 'Product updated successfully!');
    }

    public function deleteProduct(Product $product)
    {
        $this->authorize('delete', $product);
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function orders()
    {
        $orders = auth()->user()->ordersAsSeller()->with(['buyer', 'items.product'])->latest()->paginate(10);
        return view('farmer.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $this->authorize('updateStatus', $order);

        $allowedValues = collect(OrderStatus::cases())->map(fn($s) => $s->value)->implode(',');
        $request->validate(['status' => "required|in:{$allowedValues}"]);

        $newStatus = OrderStatus::from($request->status);

        if ($newStatus === OrderStatus::Delivered) {
            return back()->with('error', 'Only the buyer can confirm delivery via OTP.');
        }

        try {
            $this->orderService->transition($order, $newStatus, auth()->user(), $request->notes);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Generate OTP when order is shipped
        if ($newStatus === OrderStatus::Shipped) {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $order->update([
                'delivery_otp'            => $otp,
                'delivery_otp_expires_at' => now()->addHours(72),
            ]);
            try {
                Mail::to($order->buyer->email)->send(new DeliveryOtpMail($order->fresh()));
            } catch (\Exception $e) {
                // Mail failure must not block the status update
            }
        }

        return back()->with('success', 'Order status updated.');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'farm_name' => 'nullable|string|max:255',
            'bio'       => 'nullable|string|max:500',
            'street'    => 'nullable|string|max:255',
            'purok'     => 'nullable|string|max:50',
            'barangay'  => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
        ]);
        auth()->user()->update($data);
        return back()->with('success', 'Store profile updated!');
    }

    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        auth()->user()->update($data);
        return response()->json(['success' => true]);
    }

    public function toggleLive(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'is_live'    => 'required|boolean',
            'live_title' => 'nullable|string|max:100',
        ]);
        $user->update($data);
        return response()->json(['success' => true, 'is_live' => $user->is_live]);
    }

    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'id_type'       => 'nullable|string|max:100',
            'id_document'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
            'selfie_photo'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'farm_document' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);

        $user = auth()->user();
        $data = [];

        if ($request->filled('id_type')) {
            $data['id_type'] = $request->id_type;
        }
        if ($request->hasFile('id_document')) {
            $data['id_document'] = $request->file('id_document')->store('verifications/ids', 'public');
        }
        if ($request->hasFile('selfie_photo')) {
            $data['selfie_photo'] = $request->file('selfie_photo')->store('verifications/selfies', 'public');
        }
        if ($request->hasFile('farm_document')) {
            $data['farm_document'] = $request->file('farm_document')->store('verifications/farms', 'public');
        }

        if (empty($data)) {
            return back()->with('error', 'Please upload at least one document.');
        }

        $user->update($data);
        return back()->with('success', 'Documents submitted! Our team will review your application.');
    }

    public function replyToReview(Request $request, Review $review)
    {
        abort_if($review->product->user_id !== auth()->id(), 403);
        $request->validate(['reply' => 'required|string|max:1000']);
        $review->update([
            'seller_reply'    => $request->reply,
            'seller_reply_at' => now(),
        ]);
        return back()->with('success', 'Reply posted.');
    }

    public function toggleFreeDelivery()
    {
        $user = auth()->user();
        $user->update(['free_delivery' => !$user->free_delivery]);
        $status = $user->free_delivery ? 'enabled' : 'disabled';
        return back()->with('success', "Free delivery {$status}.");
    }
}
