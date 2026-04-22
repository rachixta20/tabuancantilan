<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_number', 'buyer_id', 'seller_id', 'subtotal', 'delivery_fee',
        'platform_fee', 'seller_payout', 'total', 'status', 'payment_status',
        'payment_method', 'delivery_address', 'notes', 'delivered_at',
    ];

    protected $casts = [
        'delivered_at'   => 'datetime',
        'status'         => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function statusHistories() { return $this->hasMany(OrderStatusHistory::class)->latest(); }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    public function canBeCancelledByBuyer(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::Confirmed], true);
    }

    public function canTransitionTo(OrderStatus $next): bool
    {
        return $this->status?->canTransitionTo($next) ?? false;
    }

    public static function generateOrderNumber(): string
    {
        return 'TBN-' . strtoupper(Str::random(10));
    }
}
