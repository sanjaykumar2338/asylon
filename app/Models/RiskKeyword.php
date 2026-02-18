<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_id',
        'phrase',
        'weight',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }
}
