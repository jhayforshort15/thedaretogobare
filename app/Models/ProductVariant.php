<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'printify_variant_id',
        'product_id', 'size', 'color', 'color_hex', 'image', 'sku', 'price_override', 'stock',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Selling price for this variant — its override, else the product price.
     */
    public function priceFor(Product $product): float
    {
        return (float) ($this->price_override ?? $product->price);
    }
}
