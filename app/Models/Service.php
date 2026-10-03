<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Publishable;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
