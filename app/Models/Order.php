<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id',
        'email', 'first_name', 'last_name', 'phone',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_postal_code', 'shipping_country',
        'subtotal', 'shipping_cost', 'tax', 'total',
        'status', 'payment_status', 'payment_method', 'payment_reference', 'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
