<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_variant_id', 'name', 'size', 'color', 'price', 'quantity', 'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * "Size M, Black" style label for receipts and emails.
     */
    public function optionsLabel(): ?string
    {
        $parts = array_filter([
            $this->size ? "Size {$this->size}" : null,
            $this->color,
        ]);

        return $parts ? implode(', ', $parts) : null;
    }
}
