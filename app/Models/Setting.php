<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'json'];

    /** @return array<string, mixed> */
    public static function values(): array
    {
        return once(fn () => Cache::rememberForever('settings', fn () => static::pluck('value', 'key')->all()));
    }

    /** The CMS upload wins; the bundled BASolution logo is the fallback. */
    public static function logoUrl(): string
    {
        $logo = static::values()['logo'] ?? null;

        return filled($logo) ? Storage::url($logo) : asset('images/ba-logo-web.png');
    }

    /** @param array<string, mixed> $data */
    public static function saveMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings');
    }
}
