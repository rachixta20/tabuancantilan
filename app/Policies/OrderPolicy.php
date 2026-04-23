<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id || $user->id === $order->seller_id || $user->isAdmin();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id && $order->canBeCancelledByBuyer();
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->id === $order->seller_id || $user->isAdmin();
    }

    public function confirmReceipt(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id && $order->status === \App\Enums\OrderStatus::Shipped;
    }

    public function review(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id;
    }
}
