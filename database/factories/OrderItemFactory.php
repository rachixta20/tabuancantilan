<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItem> */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $price    = fake()->randomFloat(2, 10, 300);
        $quantity = fake()->numberBetween(1, 10);
        return [
            'order_id'     => Order::factory(),
            'product_id'   => Product::factory(),
            'product_name' => fake()->words(3, true),
            'price'        => $price,
            'quantity'     => $quantity,
            'unit'         => 'kg',
            'subtotal'     => $price * $quantity,
        ];
    }
}
