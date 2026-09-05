<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Men', 'position' => 1],
            ['name' => 'Women', 'position' => 2],
            ['name' => 'Unisex', 'position' => 3],
            ['name' => 'Fight Gear', 'position' => 4],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'position' => $cat['position'], 'is_active' => true],
            );
        }

        $brands = ['Fairtex', 'BKFC', 'Kingdom Muay', 'Yuth', 'DRC'];
        foreach ($brands as $i => $name) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'position' => $i + 1, 'is_active' => true],
            );
        }

        $men = Category::where('slug', 'men')->first();
        $unisex = Category::where('slug', 'unisex')->first();
        $fightGear = Category::where('slug', 'fight-gear')->first();

        $products = [
            [
                'name' => 'D2GB Bad Intentions Tee',
                'price' => 34.99,
                'category_id' => $men?->id,
                'is_featured' => true,
                'short_description' => 'Heavyweight cotton tee with bold D2GB front graphic.',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'name' => 'D2GB Classic Logo Tee',
                'price' => 29.99,
                'category_id' => $unisex?->id,
                'is_featured' => true,
                'short_description' => 'The everyday classic logo tee. Soft, durable, unmistakable.',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'name' => 'D2GB Fight Shorts',
                'price' => 49.99,
                'category_id' => $fightGear?->id,
                'is_featured' => true,
                'short_description' => 'Performance fight shorts built for the bare knuckle grind.',
                'sizes' => ['28', '30', '32', '34', '36'],
            ],
            [
                'name' => 'D2GB Snapback Cap',
                'price' => 24.99,
                'category_id' => $unisex?->id,
                'is_featured' => true,
                'short_description' => 'Embroidered snapback with the D2GB fist mark.',
                'sizes' => ['One Size'],
            ],
        ];

        foreach ($products as $data) {
            $sizes = $data['sizes'];
            unset($data['sizes']);

            $data['slug'] = Str::slug($data['name']);
            $data['sku'] = 'D2GB-' . strtoupper(Str::random(6));
            $data['stock'] = 100;
            $data['is_active'] = true;

            $product = Product::updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($sizes as $size) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $size, 'color' => null],
                    ['stock' => 25, 'sku' => $product->sku . '-' . Str::slug($size)],
                );
            }
        }
    }
}
