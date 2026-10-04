<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class PrintifyProductImporter
{
    // Print-on-demand: treat stock as effectively unlimited.
    public const POD_STOCK = 9999;

    /**
     * Create or update a catalog product from a Printify product payload.
     * Returns null when the product has no enabled variants.
     */
    public function import(array $p): ?Product
    {
        $enabledVariants = collect($p['variants'] ?? [])->where('is_enabled', true);
        if ($enabledVariants->isEmpty()) {
            return null;
        }

        $basePrice = $enabledVariants->min('price') / 100; // Printify prices are in cents
        $defaultImage = collect($p['images'] ?? [])->firstWhere('is_default', true)['src']
            ?? (collect($p['images'] ?? [])->first()['src'] ?? null);

        $product = Product::firstOrNew(['printify_product_id' => $p['id']]);

        // New Printify products show on the homepage by default; admins can untick it later.
        if (! $product->exists) {
            $product->is_featured = true;
        }

        $product->fill([
            'name' => $p['title'],
            'slug' => $this->uniqueSlug($p['title'], $p['id']),
            'short_description' => Str::limit(strip_tags($p['description'] ?? ''), 150),
            'description' => strip_tags($p['description'] ?? ''),
            'price' => $basePrice,
            'image' => $defaultImage,
            'stock' => self::POD_STOCK,
            'is_active' => (bool) ($p['visible'] ?? true),
        ])->save();

        // Variants — drop ones that were disabled or removed in Printify.
        $product->variants()
            ->whereNotNull('printify_variant_id')
            ->whereNotIn('printify_variant_id', $enabledVariants->pluck('id'))
            ->delete();

        foreach ($enabledVariants as $v) {
            [$size, $color] = $this->parseTitle($v['title'] ?? '');
            $price = ($v['price'] ?? 0) / 100;

            $product->variants()->updateOrCreate(
                ['printify_variant_id' => $v['id']],
                [
                    'size' => $size,
                    'color' => $color,
                    'sku' => $v['sku'] ?? null,
                    'price_override' => $price != $basePrice ? $price : null,
                    'stock' => self::POD_STOCK,
                ],
            );
        }

        // Gallery — replace with the current Printify images.
        $product->images()->delete();
        foreach (collect($p['images'] ?? [])->take(8) as $i => $img) {
            $product->images()->create([
                'path' => $img['src'],
                'alt' => $p['title'],
                'position' => $i,
            ]);
        }

        return $product;
    }

    protected function parseTitle(string $title): array
    {
        $parts = array_map('trim', explode('/', $title));

        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    protected function uniqueSlug(string $title, string $printifyId): string
    {
        $slug = Str::slug($title);
        $exists = Product::where('slug', $slug)
            ->where('printify_product_id', '!=', $printifyId)
            ->exists();

        return $exists ? $slug.'-'.substr($printifyId, -5) : $slug;
    }
}
