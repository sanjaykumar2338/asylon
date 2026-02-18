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
     * In-memory cache of settings for the current request.
     *
     * @var array<string, mixed>
     */
    protected static array $cache = [];

    /**
     * Indicates if the cache has been populated.
     */
    protected static bool $cacheLoaded = false;

    /**
     * Retrieve a setting value with an optional default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        static::ensureCacheLoaded();

        return array_key_exists($key, static::$cache)
            ? static::$cache[$key]
            : $default;
    }

    /**
     * Persist a setting key/value pair.
     */
    public static function set(string $key, mixed $value): self
    {
        $record = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Keep the runtime cache in sync so subsequent reads stay fast.
        static::$cache[$key] = $record->value;

        return $record;
    }

    /**
     * Flush the cached settings (mainly useful for tests).
     */
    public static function flushCache(): void
    {
        static::$cache = [];
        static::$cacheLoaded = false;
    }

    /**
     * Load all settings into the runtime cache once per request.
     */
    protected static function ensureCacheLoaded(): void
    {
        if (static::$cacheLoaded) {
            return;
        }

        static::$cache = static::query()
            ->get(['key', 'value'])
            ->mapWithKeys(static fn (self $setting): array => [
                $setting->key => $setting->value,
            ])
            ->all();

        static::$cacheLoaded = true;
    }
}
