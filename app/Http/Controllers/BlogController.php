<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('blog/Index', [
            'posts' => Post::where('is_published', true)
                ->orderByDesc('published_at')
                ->get(['title', 'slug', 'excerpt', 'image', 'published_at'])
                ->map(fn (Post $p) => [
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'excerpt' => $p->excerpt,
                    'image' => $p->image_url,
                    'date' => $p->published_at?->format('M j, Y'),
                ]),
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->is_published, 404);

        return Inertia::render('blog/Show', [
            'post' => [
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'image' => $post->image_url,
                'date' => $post->published_at?->format('M j, Y'),
            ],
        ]);
    }
}
