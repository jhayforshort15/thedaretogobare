<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'printify_product_id',
        'category_id', 'brand_id', 'name', 'slug', 'sku',
        'short_description', 'description', 'price', 'compare_at_price',
        'image', 'stock', 'is_featured', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Public, web-accessible URL for the product image (or null).
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->image) {
                return null;
            }

            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }

            // Root-relative URL so it resolves against the current origin
            // (works across dev ports and production domains alike).
            $path = ltrim(parse_url(Storage::disk('public')->url($this->image), PHP_URL_PATH) ?? '', '/');

            return '/'.$path;
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Find the variant matching the customer's size/colour choice.
     * Returns an error message instead when the choice is incomplete or doesn't exist.
     */
    public function resolveVariant(?string $size, ?string $color): ProductVariant|string|null
    {
        $variants = $this->variants;

        if ($variants->isEmpty()) {
            return null;
        }

        if ($variants->whereNotNull('color')->isNotEmpty() && empty($color)) {
            return 'Please choose a color first.';
        }

        if ($variants->whereNotNull('size')->isNotEmpty() && empty($size)) {
            return 'Please choose a size first.';
        }

        return $variants->first(fn (ProductVariant $v) => $v->size == $size && $v->color == $color)
            ?? 'That size and color combination is not available.';
    }

    /**
     * Reduce stock for this product (and the chosen variant) after a sale.
     */
    public function decrementStock(?ProductVariant $variant, int $quantity): void
    {
        $this->decrement('stock', min($quantity, $this->stock));

        $variant?->decrement('stock', min($quantity, $variant->stock));
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }
}
