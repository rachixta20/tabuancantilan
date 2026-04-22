<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->paragraph(),
            'price'       => fake()->randomFloat(2, 10, 500),
            'unit'        => fake()->randomElement(['kg', 'piece', 'bundle', 'tray']),
            'stock'       => fake()->numberBetween(5, 200),
            'location'    => 'Cantilan, Surigao del Sur',
            'status'      => 'active',
            'is_organic'  => false,
            'is_featured' => false,
            'avg_rating'  => 0,
            'total_reviews' => 0,
            'total_sold'  => 0,
        ];
    }
}
