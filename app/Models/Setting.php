<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'encrypted',
    ];

    /**
     * Retrieve a setting value with an optional default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $record = static::query()->where('key', $key)->first();

        return $record?->value ?? $default;
    }

    /**
     * Persist a setting key/value pair.
     */
    public static function set(string $key, mixed $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
