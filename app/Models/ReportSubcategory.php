<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSubcategory extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'report_category_id',
        'name',
        'description',
        'position',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'report_category_id' => 'integer',
        'position' => 'integer',
    ];

    /**
     * @return BelongsTo<ReportCategory, ReportSubcategory>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'report_category_id');
    }
}
