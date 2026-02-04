<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'name_translations',
    ];

    protected $casts = [
        'name_translations' => 'array',
    ];

    protected array $translatable = [
        'name',
    ];

    protected static function booted(): void
    {
        static::creating(function (BlogCategory $category) {
            if (blank($category->slug)) {
                $rawName = $category->getAttributeFromArray('name') ?? $category->name;
                $category->slug = Str::slug($rawName);
            }
        });
    }

    public function getNameAttribute($value): ?string
    {
        return $this->resolveTranslation('name', $value);
    }

    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $translations = $this->getAttribute($field.'_translations') ?? [];

        if (is_array($translations)) {
            $value = $translations[$locale] ?? null;

            if (filled($value)) {
                return $value;
            }

            if ($fallbackLocale && $locale !== $fallbackLocale) {
                $fallbackValue = $translations[$fallbackLocale] ?? null;
                if (filled($fallbackValue)) {
                    return $fallbackValue;
                }
            }
        }

        return $this->getRawOriginal($field);
    }

    public function getTranslationValue(string $field, string $locale): ?string
    {
        $translations = $this->getAttribute($field.'_translations') ?? [];

        return is_array($translations) ? ($translations[$locale] ?? null) : null;
    }

    public function setTranslation(string $field, string $locale, $value): void
    {
        if (! in_array($field, $this->translatable, true)) {
            return;
        }

        $translations = $this->getAttribute($field.'_translations') ?? [];
        $translations[$locale] = $value;
        $this->setAttribute($field.'_translations', $translations);
    }

    protected function resolveTranslation(string $field, $fallback)
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $translations = $this->getAttribute($field.'_translations') ?? [];

        if (is_array($translations)) {
            $value = $translations[$locale] ?? null;
            if (filled($value)) {
                return $value;
            }

            if ($fallbackLocale && $locale !== $fallbackLocale) {
                $fallbackValue = $translations[$fallbackLocale] ?? null;
                if (filled($fallbackValue)) {
                    return $fallbackValue;
                }
            }
        }

        return $fallback;
    }

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
