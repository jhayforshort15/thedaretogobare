<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\PrintifyService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PrintifyImport extends Command
{
    protected $signature = 'printify:import';

    protected $description = 'Import products, variants and images from Printify into the catalog';

    // Print-on-demand: treat stock as effectively unlimited.
    protected const POD_STOCK = 9999;

    public function handle(PrintifyService $printify): int
    {
        if (! $printify->enabled()) {
            $this->error('Printify is not configured. Set PRINTIFY_API_TOKEN and PRINTIFY_SHOP_ID in .env.');

            return self::FAILURE;
        }

        $count = 0;

        $printify->eachProduct(function (array $p) use (&$count) {
            $this->importProduct($p);
            $count++;
            $this->line("Imported: {$p['title']}");
        });

        $this->info("Done. {$count} product(s) synced from Printify.");

        return self::SUCCESS;
    }

    protected function importProduct(array $p): void
    {
        $enabledVariants = collect($p['variants'] ?? [])->where('is_enabled', true);
        if ($enabledVariants->isEmpty()) {
            return;
        }

        $basePrice = $enabledVariants->min('price') / 100; // Printify prices are in cents
        $defaultImage = collect($p['images'] ?? [])->firstWhere('is_default', true)['src']
            ?? (collect($p['images'] ?? [])->first()['src'] ?? null);

        $product = Product::updateOrCreate(
            ['printify_product_id' => $p['id']],
            [
                'name' => $p['title'],
                'slug' => $this->uniqueSlug($p['title'], $p['id']),
                'short_description' => Str::limit(strip_tags($p['description'] ?? ''), 150),
                'description' => strip_tags($p['description'] ?? ''),
                'price' => $basePrice,
                'image' => $defaultImage,
                'stock' => self::POD_STOCK,
                'is_active' => (bool) ($p['visible'] ?? true),
            ],
        );

        // Variants
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
