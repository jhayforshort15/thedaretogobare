<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    protected string $sessionKey = 'cart';

    /**
     * Raw session lines: [ rowId => ['product_id' => int, 'size' => ?string, 'quantity' => int] ]
     *
     * @return array<string, array{product_id:int, size:?string, quantity:int}>
     */
    protected function lines(): array
    {
        return session()->get($this->sessionKey, []);
    }

    protected function save(array $lines): void
    {
        session()->put($this->sessionKey, $lines);
    }

    protected function rowId(int $productId, ?string $size): string
    {
        return $productId.'-'.($size ?: 'default');
    }

    public function add(Product $product, ?string $size, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $lines = $this->lines();
        $rowId = $this->rowId($product->id, $size);

        if (isset($lines[$rowId])) {
            $lines[$rowId]['quantity'] += $quantity;
        } else {
            $lines[$rowId] = [
                'product_id' => $product->id,
                'size' => $size,
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

        return collect($lines)
            ->map(function (array $line, string $rowId) use ($products) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    return null;
                }

                $price = (float) $product->price;

                return [
                    'row_id' => $rowId,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image_url,
                    'size' => $line['size'],
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
