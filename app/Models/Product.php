<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'name', 'slug', 'description', 'price',
        'original_price', 'unit', 'stock', 'image', 'images', 'location',
        'is_organic', 'is_featured', 'status', 'avg_rating', 'total_reviews', 'total_sold',
    ];

    protected $casts = [
        'images'         => 'array',
        'is_organic'     => 'boolean',
        'is_featured'    => 'boolean',
        'price'          => 'decimal:2',
        'original_price' => 'decimal:2',
        'status'         => ProductStatus::class,
    ];

    public function seller() { return $this->belongsTo(User::class, 'user_id'); }
    public function category() { return $this->belongsTo(Category::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) return asset('storage/' . $this->image);
        return asset('images/product-placeholder.jpg');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return null;
    }

    public function isActive(): bool
    {
        return $this->status === ProductStatus::Active;
    }

    public function scopeActive($query) { return $query->where('status', ProductStatus::Active->value); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeInLocation($query, string $location) {
        return $query->where('location', 'like', "%{$location}%");
    }
}
