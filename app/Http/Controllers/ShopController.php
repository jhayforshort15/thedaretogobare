<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $activeCategory = $request->query('category');

        $products = Product::query()
            ->where('is_active', true)
            ->when($activeCategory, function ($query) use ($activeCategory) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $activeCategory));
            })
            ->withCount(['variants as size_count' => fn ($q) => $q->whereNotNull('size')])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'price', 'compare_at_price', 'image', 'category_id', 'stock'])
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'compare_at_price' => $p->compare_at_price ? (float) $p->compare_at_price : null,
                'image' => $p->image_url,
                'has_sizes' => $p->size_count > 0,
                'in_stock' => $p->stock > 0,
            ]);

        return Inertia::render('shop/Index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)
                ->orderBy('position')
                ->get(['id', 'name', 'slug']),
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load(['variants', 'images', 'category', 'brand']);

        return Inertia::render('shop/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'compare_at_price' => $product->compare_at_price ? (float) $product->compare_at_price : null,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'image' => $product->image_url,
                'stock' => $product->stock,
                'category' => $product->category?->only(['name', 'slug']),
                'brand' => $product->brand?->only(['name', 'slug']),
                'sizes' => $this->sortSizes($product->variants->pluck('size')->filter()->unique()->values()->all()),
                'colors' => $product->variants->whereNotNull('color')->unique('color')
                    ->map(fn ($v) => ['name' => $v->color, 'hex' => $v->color_hex])->values(),
                'variants' => $product->variants->map(fn ($v) => [
                    'size' => $v->size,
                    'color' => $v->color,
                    'price' => $v->priceFor($product),
                    'image' => $v->image,
                    'in_stock' => $v->stock > 0,
                ])->values(),
                'images' => $product->images->map(fn ($img) => ['path' => $img->path, 'alt' => $img->alt]),
            ],
            'related' => Product::where('is_active', true)
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
                ->inRandomOrder()
                ->take(4)
                ->get(['id', 'name', 'slug', 'price', 'image'])
                ->map(fn (Product $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => (float) $p->price,
                    'image' => $p->image_url,
                ]),
        ]);
    }

    /**
     * Order apparel sizes naturally (XS, S, M, L, XL, 2XL…); others keep their order after.
     */
    protected function sortSizes(array $sizes): array
    {
        $order = ['XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', 'XXL', '3XL', 'XXXL', '4XL', '5XL', '6XL'];

        usort($sizes, function ($a, $b) use ($order) {
            $ia = array_search(strtoupper($a), $order);
            $ib = array_search(strtoupper($b), $order);

            return ($ia === false ? PHP_INT_MAX : $ia) <=> ($ib === false ? PHP_INT_MAX : $ib);
        });

        return $sizes;
    }
}
