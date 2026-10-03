<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    public function initializePublishable(): void
    {
        $this->mergeCasts(['is_active' => 'boolean']);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}
