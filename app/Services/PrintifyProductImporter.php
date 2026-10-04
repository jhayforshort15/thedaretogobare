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

        $options = $this->optionLookup($p['options'] ?? []);

        foreach ($enabledVariants as $v) {
            ['size' => $size, 'color' => $color, 'color_hex' => $colorHex] = $this->variantOptions($v, $options);
            $price = ($v['price'] ?? 0) / 100;

            $product->variants()->updateOrCreate(
                ['printify_variant_id' => $v['id']],
                [
                    'size' => $size,
                    'color' => $color,
                    'color_hex' => $colorHex,
                    'image' => $this->variantImage($p['images'] ?? [], $v['id']),
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

    /**
     * Map Printify option value ids to their type, title and swatch colour.
     *
     * @return array<int, array{type:string, title:string, hex:?string}>
     */
    protected function optionLookup(array $options): array
    {
        $lookup = [];

        foreach ($options as $option) {
            foreach ($option['values'] ?? [] as $value) {
                $lookup[$value['id']] = [
                    'type' => $option['type'] ?? '',
                    'title' => $value['title'] ?? '',
                    'hex' => $value['colors'][0] ?? null,
                ];
            }
        }

        return $lookup;
    }

    /**
     * Work out a variant's size and colour from its option ids.
     * Falls back to the "Color / Size" title when options are missing.
     */
    protected function variantOptions(array $variant, array $lookup): array
    {
        $result = ['size' => null, 'color' => null, 'color_hex' => null];
        $other = [];

        foreach ($variant['options'] ?? [] as $id) {
            $value = $lookup[$id] ?? null;

            if (! $value) {
                continue;
            }

            if ($value['type'] === 'color') {
                $result['color'] = $value['title'];
                $result['color_hex'] = $value['hex'];
            } elseif ($value['type'] === 'size') {
                $result['size'] = $value['title'];
            } else {
                $other[] = $value['title'];
            }
        }

        // Non-apparel options (e.g. "11oz", "Glossy") are shown as the size choice.
        if (! $result['size'] && $other) {
            $result['size'] = implode(' / ', $other);
        }

        if (! $result['size'] && ! $result['color']) {
            $parts = array_map('trim', explode('/', $variant['title'] ?? ''));
            $result['color'] = count($parts) > 1 ? $parts[0] : null;
            $result['size'] = end($parts) ?: null;
        }

        return $result;
    }

    /**
     * The best image for a variant: its default mockup, else its first one.
     */
    protected function variantImage(array $images, int $variantId): ?string
    {
        $forVariant = collect($images)->filter(fn ($img) => in_array($variantId, $img['variant_ids'] ?? []));

        return ($forVariant->firstWhere('is_default', true) ?? $forVariant->first())['src'] ?? null;
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
