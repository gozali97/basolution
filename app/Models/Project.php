<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use Publishable;

    protected $casts = [
        'tech_stack' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
