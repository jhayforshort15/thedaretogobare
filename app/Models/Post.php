<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image', 'published_at', 'is_published',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->image) {
                return null;
            }

            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }

            $path = ltrim(parse_url(Storage::disk('public')->url($this->image), PHP_URL_PATH) ?? '', '/');

            return '/'.$path;
        });
    }
}
