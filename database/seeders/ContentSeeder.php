<?php

namespace Database\Seeders;

use App\Models\Fight;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $fights = [
            ['title' => 'BKB 44 Denver Brawl', 'date_label' => 'AUG 16', 'location' => 'Denver, Colorado, USA', 'position' => 1],
            ['title' => 'Bare Knuckle Fighting Championship', 'date_label' => '', 'location' => 'New York, USA', 'position' => 2],
            ['title' => 'BKB 45 London', 'date_label' => '', 'location' => 'London, UK', 'position' => 3],
        ];

        foreach ($fights as $fight) {
            Fight::updateOrCreate(
                ['title' => $fight['title']],
                [...$fight, 'is_active' => true],
            );
        }

        $posts = [
            [
                'title' => '5 Training Tips From Bare Knuckle Fighters',
                'published_at' => '2024-05-20',
                'excerpt' => 'Hard-won lessons from the ring to sharpen your training.',
            ],
            [
                'title' => 'Mindset of a Warrior: Train Hard, Stay Humble',
                'published_at' => '2024-05-15',
                'excerpt' => 'Why discipline and humility win more fights than ego.',
            ],
            [
                'title' => 'BKB 44 Fight Preview: What to Expect',
                'published_at' => '2024-05-10',
                'excerpt' => 'The matchups, the stakes, and who to watch at BKB 44.',
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                [
                    ...$post,
                    'slug' => Str::slug($post['title']),
                    'body' => "This is placeholder article content for \"{$post['title']}\". Edit it in the admin panel.\n\nDare To Go Bare — no gloves, no excuses.",
                    'is_published' => true,
                ],
            );
        }
    }
}
