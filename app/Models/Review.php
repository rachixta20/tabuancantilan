<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'order_id', 'rating', 'comment', 'seller_reply', 'seller_reply_at'];

    protected function casts(): array
    {
        return ['seller_reply_at' => 'datetime'];
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
}
