<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'template',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
        'title_translations',
        'excerpt_translations',
        'meta_title_translations',
        'meta_description_translations',
        'meta_keywords_translations',
        'content_translations',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'title_translations' => 'array',
        'excerpt_translations' => 'array',
        'meta_title_translations' => 'array',
        'meta_description_translations' => 'array',
        'meta_keywords_translations' => 'array',
        'content_translations' => 'array',
    ];

    protected array $translatable = [
        'title',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'content',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (blank($page->slug)) {
                $rawTitle = $page->getAttributeFromArray('title') ?? $page->title;
                $page->slug = Str::slug($rawTitle);
            }
        });
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->resolveTranslation('title', $value);
    }

    public function getExcerptAttribute($value): ?string
    {
        return $this->resolveTranslation('excerpt', $value);
    }

    public function getMetaTitleAttribute($value): ?string
    {
        return $this->resolveTranslation('meta_title', $value);
    }

    public function getMetaDescriptionAttribute($value): ?string
    {
        return $this->resolveTranslation('meta_description', $value);
    }

    public function getMetaKeywordsAttribute($value): ?string
    {
        return $this->resolveTranslation('meta_keywords', $value);
    }

    public function getContentAttribute($value): ?string
    {
        return $this->resolveTranslation('content', $value);
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

    public function url(): string
    {
        return route('pages.show', $this->slug);
    }
}
