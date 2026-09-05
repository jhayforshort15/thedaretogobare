<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
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
                ->orderBy('id')
                ->take(8)
                ->get(['id', 'name', 'slug', 'price', 'image'])
                ->map(fn (Product $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => (float) $p->price,
                    'image' => $p->image,
                ]),

            'brands' => Brand::where('is_active', true)
                ->orderBy('position')
                ->pluck('name'),

            // Content sections — still placeholder until a CMS/content phase.
            'fights' => [
                ['title' => 'BKB 44 Denver Brawl', 'date' => 'AUG 16', 'location' => 'Denver, Colorado, USA'],
                ['title' => 'Bare Knuckle Fighting Championship', 'date' => '', 'location' => 'New York, USA'],
                ['title' => 'BKB 45 London', 'date' => '', 'location' => 'London, UK'],
            ],
            'posts' => [
                ['title' => '5 Training Tips From Bare Knuckle Fighters', 'date' => 'MAY 20, 2024', 'slug' => 'training-tips'],
                ['title' => 'Mindset of a Warrior: Train Hard, Stay Humble', 'date' => 'MAY 15, 2024', 'slug' => 'warrior-mindset'],
                ['title' => 'BKB 44 Fight Preview: What to Expect', 'date' => 'MAY 10, 2024', 'slug' => 'bkb-44-preview'],
            ],
        ]);
    }
}
