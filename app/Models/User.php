<?php

namespace App\Models;

use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'avatar', 'role', 'location', 'bio',
        'is_verified', 'is_active', 'account_status', 'rejection_reason', 'verified_at',
        'id_document', 'selfie_photo', 'farm_document', 'id_type', 'farm_name', 'admin_notes',
        'street', 'purok', 'barangay',
        'latitude', 'longitude', 'is_live', 'live_title',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['avatar_url', 'full_address'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at'       => 'datetime',
            'is_verified'       => 'boolean',
            'is_active'         => 'boolean',
            'is_live'           => 'boolean',
            'latitude'          => 'float',
            'longitude'         => 'float',
            'password'          => 'hashed',
            'account_status'    => UserStatus::class,
        ];
    }

    public function isPending(): bool   { return $this->account_status === UserStatus::Pending; }
    public function isApproved(): bool  { return $this->account_status === UserStatus::Approved; }
    public function isRejected(): bool  { return $this->account_status === UserStatus::Rejected; }
    public function isSuspended(): bool { return $this->account_status === UserStatus::Suspended; }

    public function canSellProducts(): bool
    {
        return $this->isFarmer() && $this->isApproved() && $this->is_active;
    }

    public function isFarmer(): bool   { return $this->role === 'farmer'; }
    public function isBuyer(): bool    { return $this->role === 'buyer'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isVerifier(): bool { return $this->role === 'verifier'; }

    public function products() { return $this->hasMany(Product::class); }
    public function ordersAsBuyer() { return $this->hasMany(Order::class, 'buyer_id'); }
    public function ordersAsSeller() { return $this->hasMany(Order::class, 'seller_id'); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }
    public function sentMessages() { return $this->hasMany(Message::class, 'sender_id'); }
    public function auditLogs() { return $this->hasMany(AdminAuditLog::class, 'admin_id'); }

    public function getFullAddressAttribute(): string
    {
        $location = config('marketplace.city', 'Cantilan, Surigao del Sur');
        $parts = array_filter([
            $this->street,
            $this->purok ? 'Purok ' . $this->purok : null,
            $this->barangay ? 'Brgy. ' . $this->barangay : null,
            $location,
        ]);
        return implode(', ', $parts);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) return asset('storage/' . $this->avatar);
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=16a34a&color=fff&size=128";
    }

    public function cartCount(): int
    {
        return $this->carts()->sum('quantity');
    }

    public function unreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function ($q) {
            $q->where('buyer_id', $this->id)->orWhere('seller_id', $this->id);
        })->where('sender_id', '!=', $this->id)->where('is_read', false)->count();
    }

    public function averageSellerRating(): float
    {
        return $this->products()
            ->join('reviews', 'products.id', '=', 'reviews.product_id')
            ->avg('reviews.rating') ?? 0;
    }
}
