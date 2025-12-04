<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'page_id',
        'title',
        'type',
        'url',
        'target',
        'position',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('position');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function resolvedUrl(): string
    {
        if ($this->type === 'page' && $this->page) {
            return route('pages.show', $this->page->slug);
        }

        $url = trim((string) ($this->url ?? ''));

        if ($url === '') {
            return '#';
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return url($url);
    }
}
