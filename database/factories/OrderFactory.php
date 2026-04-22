<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal    = fake()->randomFloat(2, 50, 1000);
        $deliveryFee = 50;
        return [
            'order_number'     => Order::generateOrderNumber(),
            'buyer_id'         => User::factory(),
            'seller_id'        => User::factory(),
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $subtotal + $deliveryFee,
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'payment_method'   => 'cod',
            'delivery_address' => fake()->address(),
            'notes'            => null,
        ];
    }
}
