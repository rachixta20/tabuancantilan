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
use BackedEnum;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Make BackedEnum auto-convert to its value when echoed in {{ }}
        Blade::stringable(function (BackedEnum $enum) {
            return $enum->value;
        });

        // Load admin-configured settings into config
        try {
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
            if ($settings->isNotEmpty()) {
                config([
                    'marketplace.commission_rate' => $settings->get('commission_rate', config('marketplace.commission_rate')),
                    'marketplace.delivery_fee'    => $settings->get('delivery_fee', config('marketplace.delivery_fee')),
                    'marketplace.location'        => $settings->get('location', config('marketplace.location')),
                    'marketplace.city'            => $settings->get('city', config('marketplace.city')),
                ]);
            }
        } catch (\Exception $e) {
            // settings table may not exist during migrations
        }

        // Policies
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        // Events
        Event::listen(OrderPlaced::class, SendOrderPlacedNotification::class);
        Event::listen(SellerApproved::class, SendSellerApprovedNotification::class);
    }
}
