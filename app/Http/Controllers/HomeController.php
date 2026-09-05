<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        // NOTE: Placeholder data. Once the e-commerce models exist,
        // swap these arrays for Eloquent queries (Product, Category, etc.).
        return Inertia::render('Home', [
            'categories' => [
                ['name' => 'Men', 'slug' => 'men'],
                ['name' => 'Women', 'slug' => 'women'],
                ['name' => 'Unisex', 'slug' => 'unisex'],
                ['name' => 'Fight Gear', 'slug' => 'fight-gear'],
            ],
            'bestSellers' => [
                ['name' => 'D2GB Bad Intentions Tee', 'price' => 34.99, 'slug' => 'bad-intentions-tee'],
                ['name' => 'D2GB Classic Logo Tee', 'price' => 29.99, 'slug' => 'classic-logo-tee'],
                ['name' => 'D2GB Fight Shorts', 'price' => 49.99, 'slug' => 'fight-shorts'],
                ['name' => 'D2GB Snapback Cap', 'price' => 24.99, 'slug' => 'snapback-cap'],
            ],
            'brands' => ['Fairtex', 'BKFC', 'Kingdom Muay', 'Yuth', 'DRC'],
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
