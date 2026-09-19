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

    /**
     * Full order detail for the customer-facing detail / lookup pages.
     */
    public function toDisplayArray(): array
    {
        $this->loadMissing('items');

        return [
            'order_number' => $this->order_number,
            'placed_at' => $this->created_at?->format('M j, Y g:i A'),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'shipping' => [
                'address' => $this->shipping_address,
                'city' => $this->shipping_city,
                'state' => $this->shipping_state,
                'postal_code' => $this->shipping_postal_code,
                'country' => $this->shipping_country,
            ],
            'subtotal' => (float) $this->subtotal,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'items' => $this->items->map(fn (OrderItem $i) => [
                'name' => $i->name,
                'size' => $i->size,
                'price' => (float) $i->price,
                'quantity' => $i->quantity,
                'subtotal' => (float) $i->subtotal,
            ])->all(),
        ];
    }
}
