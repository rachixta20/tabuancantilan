<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $farmer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmer = User::factory()->create(['role' => 'farmer', 'account_status' => 'approved']);
        $this->buyer  = User::factory()->create(['role' => 'buyer', 'account_status' => 'approved']);

        $category      = Category::factory()->create();
        $this->product = Product::factory()->create([
            'user_id'     => $this->farmer->id,
            'category_id' => $category->id,
            'status'      => 'active',
            'stock'       => 100,
            'price'       => 50.00,
        ]);
    }

    public function test_guest_cannot_add_to_cart(): void
    {
        $this->post("/cart/{$this->product->id}", ['quantity' => 1])
             ->assertRedirect('/login');
    }

    public function test_buyer_can_add_product_to_cart(): void
    {
        $this->actingAs($this->buyer)
             ->post("/cart/{$this->product->id}", ['quantity' => 2])
             ->assertRedirect();

        $this->assertDatabaseHas('carts', [
            'user_id'    => $this->buyer->id,
            'product_id' => $this->product->id,
            'quantity'   => 2,
        ]);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        Cart::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);

        $this->actingAs($this->buyer)
             ->post("/cart/{$this->product->id}", ['quantity' => 3]);

        $this->assertDatabaseHas('carts', [
            'user_id'    => $this->buyer->id,
            'product_id' => $this->product->id,
            'quantity'   => 4,
        ]);
    }

    public function test_cannot_add_inactive_product_to_cart(): void
    {
        $this->product->update(['status' => 'inactive']);

        $this->actingAs($this->buyer)
             ->post("/cart/{$this->product->id}", ['quantity' => 1])
             ->assertSessionHas('error');

        $this->assertDatabaseMissing('carts', ['user_id' => $this->buyer->id]);
    }

    public function test_cannot_add_more_than_stock(): void
    {
        $this->actingAs($this->buyer)
             ->post("/cart/{$this->product->id}", ['quantity' => 999])
             ->assertSessionHas('error');
    }

    public function test_buyer_can_remove_own_cart_item(): void
    {
        $cart = Cart::create([
            'user_id'    => $this->buyer->id,
            'product_id' => $this->product->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($this->buyer)
             ->delete("/cart/{$cart->id}")
             ->assertRedirect();

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    public function test_buyer_cannot_remove_other_users_cart_item(): void
    {
        $otherBuyer = User::factory()->create(['role' => 'buyer', 'account_status' => 'approved']);
        $cart = Cart::create([
            'user_id'    => $otherBuyer->id,
            'product_id' => $this->product->id,
            'quantity'   => 1,
        ]);

        $this->actingAs($this->buyer)
             ->delete("/cart/{$cart->id}")
             ->assertForbidden();
    }
}
