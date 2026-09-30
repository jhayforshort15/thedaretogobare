<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fight extends Model
{
    protected $fillable = [
        'title', 'date_label', 'location', 'url', 'position', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
