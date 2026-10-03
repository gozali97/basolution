<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureRequest extends Model
{
    public const TYPES = ['feature' => 'Fitur baru', 'improvement' => 'Perbaikan / peningkatan'];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'due_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (self $request) {
            if ($request->wasChanged('status')) {
                $request->notes()->create([
                    'user_id' => auth()->id(),
                    'body' => 'Status diubah menjadi '.$request->status->getLabel().'.',
                ]);
            }
        });
    }

    public function scopeForClient(Builder $query, ?int $clientId): void
    {
        $query->whereHas('clientApp', fn (Builder $q) => $q->where('client_id', $clientId));
    }

    public function clientApp(): BelongsTo
    {
        return $this->belongsTo(ClientApp::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(FeatureRequestNote::class);
    }
}
