<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class CartService
{
    protected string $sessionKey = 'cart';

    /**
     * Raw session lines: [ rowId => ['product_id' => int, 'variant_id' => ?int, 'size' => ?string, 'color' => ?string, 'quantity' => int] ]
     *
     * @return array<string, array{product_id:int, variant_id:?int, size:?string, color:?string, quantity:int}>
     */
    protected function lines(): array
    {
        return session()->get($this->sessionKey, []);
    }

    protected function save(array $lines): void
    {
        session()->put($this->sessionKey, $lines);
    }

    public function rowId(int $productId, ?ProductVariant $variant): string
    {
        return $productId.'-'.($variant?->id ?? 'default');
    }

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $lines = $this->lines();
        $rowId = $this->rowId($product->id, $variant);

        if (isset($lines[$rowId])) {
            $lines[$rowId]['quantity'] += $quantity;
        } else {
            $lines[$rowId] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'size' => $variant?->size,
                'color' => $variant?->color,
                'quantity' => $quantity,
            ];
        }

        $this->save($lines);
    }

    public function update(string $rowId, int $quantity): void
    {
        $lines = $this->lines();

        if (! isset($lines[$rowId])) {
            return;
        }

        if ($quantity < 1) {
            unset($lines[$rowId]);
        } else {
            $lines[$rowId]['quantity'] = $quantity;
        }

        $this->save($lines);
    }

    public function remove(string $rowId): void
    {
        $lines = $this->lines();
        unset($lines[$rowId]);
        $this->save($lines);
    }

    public function clear(): void
    {
        session()->forget($this->sessionKey);
    }

    /**
     * Hydrated cart items with live product data and line subtotals.
     */
    public function items(): Collection
    {
        $lines = $this->lines();

        if (empty($lines)) {
            return collect();
        }

        $products = Product::whereIn('id', collect($lines)->pluck('product_id'))->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', collect($lines)->pluck('variant_id')->filter())->get()->keyBy('id');

        return collect($lines)
            ->map(function (array $line, string $rowId) use ($products, $variants) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    return null;
                }

                $variant = $variants->get($line['variant_id'] ?? null);
                $price = $variant ? $variant->priceFor($product) : (float) $product->price;

                return [
                    'row_id' => $rowId,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $variant?->image ?: $product->image_url,
                    'size' => $variant?->size ?? $line['size'] ?? null,
                    'color' => $variant?->color ?? $line['color'] ?? null,
                    'price' => $price,
                    'quantity' => $line['quantity'],
                    'subtotal' => round($price * $line['quantity'], 2),
                ];
            })
            ->filter()
            ->values();
    }

    public function count(): int
    {
        return collect($this->lines())->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    /** Free shipping at/over this order subtotal. */
    public const FREE_SHIPPING_THRESHOLD = 150.0;

    public const FLAT_SHIPPING = 10.0;

    public function shipping(): float
    {
        if ($this->subtotal() <= 0) {
            return 0.0;
        }

        return $this->subtotal() >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::FLAT_SHIPPING;
    }

    public function tax(): float
    {
        return 0.0; // TODO: configure tax rate when required.
    }

    public function total(): float
    {
        return round($this->subtotal() + $this->shipping() + $this->tax(), 2);
    }

    /**
     * Summary for sharing globally with the frontend.
     */
    public function summary(): array
    {
        return [
            'count' => $this->count(),
            'subtotal' => $this->subtotal(),
        ];
    }
}
