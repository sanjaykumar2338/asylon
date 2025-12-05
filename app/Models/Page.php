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
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (blank($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function url(): string
    {
        return route('pages.show', $this->slug);
    }
}
