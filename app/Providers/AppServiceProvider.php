<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\SellerApproved;
use App\Listeners\SendOrderPlacedNotification;
use App\Listeners\SendSellerApprovedNotification;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Policies\CartPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useTailwind();

        // Policies
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        // Events
        Event::listen(OrderPlaced::class, SendOrderPlacedNotification::class);
        Event::listen(SellerApproved::class, SendSellerApprovedNotification::class);
    }
}
