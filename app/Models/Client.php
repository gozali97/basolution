<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    public function apps(): HasMany
    {
        return $this->hasMany(ClientApp::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
