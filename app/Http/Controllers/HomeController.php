<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Fight;
use App\Models\Post;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'categories' => Category::where('is_active', true)
                ->orderBy('position')
                ->get(['id', 'name', 'slug', 'image']),

            'bestSellers' => Product::where('is_active', true)
                ->where('is_featured', true)
                ->withCount(['variants as size_count' => fn ($q) => $q->whereNotNull('size')])
                ->orderBy('id')
                ->take(8)
                ->get(['id', 'name', 'slug', 'price', 'image', 'stock'])
                ->map(fn (Product $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => (float) $p->price,
                    'image' => $p->image_url,
                    'has_sizes' => $p->size_count > 0,
                    'in_stock' => $p->stock > 0,
                ]),

            'brands' => Brand::where('is_active', true)
                ->orderBy('position')
                ->pluck('name'),

            'fights' => Fight::where('is_active', true)
                ->orderBy('position')
                ->take(6)
                ->get(['title', 'date_label', 'location', 'url'])
                ->map(fn (Fight $f) => [
                    'title' => $f->title,
                    'date' => $f->date_label,
                    'location' => $f->location,
                    'url' => $f->url,
                ]),

            'posts' => Post::where('is_published', true)
                ->orderByDesc('published_at')
                ->take(3)
                ->get(['title', 'slug', 'image', 'published_at'])
                ->map(fn (Post $p) => [
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'image' => $p->image_url,
                    'date' => $p->published_at?->format('M j, Y'),
                ]),
        ]);
    }
}
