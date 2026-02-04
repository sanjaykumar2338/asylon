<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'title_translations',
        'excerpt_translations',
        'content_translations',
        'featured_image_alt_translations',
        'category_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'author_name',
        'meta_title_translations',
        'meta_description_translations',
        'meta_keywords_translations',
        'author_name_translations',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'title_translations' => 'array',
        'excerpt_translations' => 'array',
        'content_translations' => 'array',
        'featured_image_alt_translations' => 'array',
        'meta_title_translations' => 'array',
        'meta_description_translations' => 'array',
        'meta_keywords_translations' => 'array',
        'author_name_translations' => 'array',
    ];

    protected array $translatable = [
        'title',
        'excerpt',
        'content',
        'featured_image_alt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'author_name',
    ];

    protected static function booted(): void
    {
        static::creating(function (BlogPost $post) {
            if (blank($post->slug)) {
                $rawTitle = $post->getAttributeFromArray('title') ?? $post->title;
                $post->slug = Str::slug($rawTitle);
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

    public function getContentAttribute($value): ?string
    {
        return $this->resolveTranslation('content', $value);
    }

    public function getFeaturedImageAltAttribute($value): ?string
    {
        return $this->resolveTranslation('featured_image_alt', $value);
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

    public function getAuthorNameAttribute($value): ?string
    {
        return $this->resolveTranslation('author_name', $value);
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

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function featuredImageUrl(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://', '//'])) {
            return $this->featured_image;
        }

        return asset('storage/'.$this->featured_image);
    }
}
