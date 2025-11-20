<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_amount', 'status', 'payment_ref',
        'delivery_name', 'delivery_phone', 'delivery_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    const STATUS_PENDING = 'pending';
    const STATUS_INITIATED = 'payment_initiated';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_REFUNDED = 'refunded';
}