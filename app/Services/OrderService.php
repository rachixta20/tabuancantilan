<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(User $buyer, Collection $cartItems, array $data): void
    {
        DB::transaction(function () use ($buyer, $cartItems, $data) {
            $paymentMethod = PaymentMethod::from($data['payment_method']);
            $grouped = $cartItems->groupBy(fn($item) => $item->product->user_id);

            foreach ($grouped as $sellerId => $items) {
                // Validate all products are still active
                foreach ($items as $item) {
                    if (!$item->product->isActive()) {
                        throw new \RuntimeException(
                            "Product '{$item->product->name}' is no longer available."
                        );
                    }
                    if ($item->product->stock < $item->quantity) {
                        throw new \RuntimeException(
                            "Insufficient stock for '{$item->product->name}'. Only {$item->product->stock} available."
                        );
                    }
                }

                $seller         = User::find($sellerId);
                $subtotal       = $items->sum(fn($item) => $item->product->price * $item->quantity);

                if ($seller?->minimum_order && $subtotal < $seller->minimum_order) {
                    $sellerName = $seller->farm_name ?: $seller->name;
                    throw new \RuntimeException(
                        "Minimum order for {$sellerName} is ₱" . number_format($seller->minimum_order, 2) . ". Your subtotal is ₱" . number_format($subtotal, 2) . "."
                    );
                }

                $deliveryFee    = ($paymentMethod->hasDeliveryFee() && !($seller?->free_delivery))
                    ? config('marketplace.delivery_fee', 50)
                    : 0;
                $commissionRate = config('marketplace.commission_rate', 5) / 100;
                $platformFee    = round($subtotal * $commissionRate, 2);
                $sellerPayout   = round($subtotal - $platformFee, 2);

                $order = Order::create([
                    'order_number'     => Order::generateOrderNumber(),
                    'buyer_id'         => $buyer->id,
                    'seller_id'        => $sellerId,
                    'subtotal'         => $subtotal,
                    'delivery_fee'     => $deliveryFee,
                    'platform_fee'     => $platformFee,
                    'seller_payout'    => $sellerPayout,
                    'total'            => $subtotal + $deliveryFee,
                    'payment_method'   => $data['payment_method'],
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'notes'            => $data['notes'] ?? null,
                ]);

                OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'status'     => OrderStatus::Pending->value,
                    'notes'      => 'Order placed',
                    'changed_by' => $buyer->id,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item->product_id,
                        'product_name' => $item->product->name,
                        'price'        => $item->product->price,
                        'quantity'     => $item->quantity,
                        'unit'         => $item->product->unit,
                        'subtotal'     => $item->product->price * $item->quantity,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                    $item->product->increment('total_sold', $item->quantity);
                }
            }

            $buyer->carts()->delete();
        });
    }

    public function transition(Order $order, OrderStatus $newStatus, User $actor, ?string $notes = null): void
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Cannot transition order from {$order->status->label()} to {$newStatus->label()}."
            );
        }

        DB::transaction(function () use ($order, $newStatus, $actor, $notes) {
            $updateData = ['status' => $newStatus->value];
            if ($newStatus === OrderStatus::Delivered) {
                $updateData['delivered_at'] = now();
            }
            $order->update($updateData);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => $newStatus->value,
                'notes'      => $notes,
                'changed_by' => $actor->id,
            ]);
        });
    }
}
