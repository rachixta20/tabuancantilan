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
        'delivery_otp', 'delivery_otp_expires_at', 'payout_status', 'payout_due_at', 'payout_released_at',
    ];

    protected $casts = [
        'delivered_at'              => 'datetime',
        'delivery_otp_expires_at'   => 'datetime',
        'payout_due_at'             => 'datetime',
        'payout_released_at'        => 'datetime',
        'status'                    => OrderStatus::class,
        'payment_status'            => PaymentStatus::class,
        'payment_method'            => PaymentMethod::class,
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

    public function isPayoutHeld(): bool     { return $this->payout_status === 'held'; }
    public function isPayoutReleased(): bool { return $this->payout_status === 'released'; }
    public function isPayoutDisputed(): bool { return $this->payout_status === 'disputed'; }

    public function canDispute(): bool
    {
        return $this->status === OrderStatus::Delivered
            && $this->payout_status === 'held'
            && $this->payout_due_at
            && now()->lessThan($this->payout_due_at);
    }

    public function payoutHoursRemaining(): int
    {
        if (!$this->payout_due_at) return 0;
        return max(0, (int) now()->diffInHours($this->payout_due_at, false));
    }

    public static function generateOrderNumber(): string
    {
        return 'TBN-' . strtoupper(Str::random(10));
    }
}
