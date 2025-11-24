<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ReportCategory extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'position',
        'type',
        'is_hidden',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'type' => 'string',
        'is_hidden' => 'boolean',
    ];

    /**
     * @return HasMany<ReportSubcategory>
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(ReportSubcategory::class)
            ->orderBy('position')
            ->orderBy('name');
    }

    /**
     * Scope visible categories.
     *
     * @param  Builder<self>  $query
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }
}
