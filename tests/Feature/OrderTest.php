<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
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
            'stock'       => 50,
            'price'       => 100.00,
        ]);
    }

    private function addToCart(int $quantity = 2): Cart
    {
        return Cart::create([
            'user_id'    => $this->buyer->id,
            'product_id' => $this->product->id,
            'quantity'   => $quantity,
        ]);
    }

    public function test_buyer_can_place_order(): void
    {
        $this->addToCart(2);

        $this->actingAs($this->buyer)
             ->post('/checkout', [
                 'payment_method'   => 'cod',
                 'delivery_address' => '123 Test St, Cantilan',
             ])
             ->assertRedirect(route('buyer.orders'));

        $this->assertDatabaseHas('orders', [
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
        ]);

        // Cart should be cleared
        $this->assertDatabaseMissing('carts', ['user_id' => $this->buyer->id]);

        // Stock should be decremented
        $this->assertEquals(48, $this->product->fresh()->stock);
    }

    public function test_order_fails_if_product_inactive(): void
    {
        $this->addToCart(2);
        $this->product->update(['status' => 'inactive']);

        $this->actingAs($this->buyer)
             ->post('/checkout', [
                 'payment_method'   => 'cod',
                 'delivery_address' => '123 Test St',
             ])
             ->assertSessionHas('error');

        $this->assertDatabaseMissing('orders', ['buyer_id' => $this->buyer->id]);
    }

    public function test_buyer_can_view_own_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
        ]);

        $this->actingAs($this->buyer)
             ->get("/buyer/orders/{$order->id}")
             ->assertStatus(200);
    }

    public function test_buyer_cannot_view_other_users_order(): void
    {
        $otherBuyer = User::factory()->create(['role' => 'buyer', 'account_status' => 'approved']);
        $order = Order::factory()->create([
            'buyer_id'  => $otherBuyer->id,
            'seller_id' => $this->farmer->id,
        ]);

        $this->actingAs($this->buyer)
             ->get("/buyer/orders/{$order->id}")
             ->assertForbidden();
    }

    public function test_buyer_can_cancel_pending_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
            'status'    => 'pending',
        ]);

        $this->actingAs($this->buyer)
             ->patch("/buyer/orders/{$order->id}/cancel")
             ->assertRedirect();

        $this->assertEquals('cancelled', $order->fresh()->status->value);
    }

    public function test_buyer_cannot_cancel_delivered_order(): void
    {
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
            'status'    => 'delivered',
        ]);

        $this->actingAs($this->buyer)
             ->patch("/buyer/orders/{$order->id}/cancel")
             ->assertForbidden();
    }

    public function test_farmer_can_update_order_status(): void
    {
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
            'status'    => 'pending',
        ]);

        $this->actingAs($this->farmer)
             ->patch("/farmer/orders/{$order->id}/status", ['status' => 'confirmed'])
             ->assertRedirect();

        $this->assertEquals('confirmed', $order->fresh()->status->value);
    }

    public function test_farmer_cannot_skip_order_status(): void
    {
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
            'status'    => 'pending',
        ]);

        $this->actingAs($this->farmer)
             ->patch("/farmer/orders/{$order->id}/status", ['status' => 'delivered'])
             ->assertSessionHas('error');

        $this->assertEquals('pending', $order->fresh()->status->value);
    }

    public function test_farmer_cannot_update_other_farmers_order(): void
    {
        $otherFarmer = User::factory()->create(['role' => 'farmer', 'account_status' => 'approved']);
        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $otherFarmer->id,
            'status'    => 'pending',
        ]);

        $this->actingAs($this->farmer)
             ->patch("/farmer/orders/{$order->id}/status", ['status' => 'confirmed'])
             ->assertForbidden();
    }

    public function test_buyer_cannot_review_product_not_in_order(): void
    {
        $otherProduct = Product::factory()->create([
            'user_id'     => $this->farmer->id,
            'category_id' => $this->product->category_id,
            'status'      => 'active',
            'stock'       => 10,
        ]);

        $order = Order::factory()->create([
            'buyer_id'  => $this->buyer->id,
            'seller_id' => $this->farmer->id,
            'status'    => 'delivered',
        ]);

        OrderItem::factory()->create([
            'order_id'   => $order->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->buyer)
             ->post("/buyer/orders/{$order->id}/review", [
                 'product_id' => $otherProduct->id,
                 'rating'     => 5,
             ])
             ->assertForbidden();
    }
}
