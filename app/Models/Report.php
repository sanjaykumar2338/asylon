<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'category',
        'subcategory',
        'description',
        'contact_name',
        'contact_email',
        'contact_phone',
        'type',
        'severity',
        'urgent',
        'status',
        'portal_source',
        'meta',
        'violation_date',
        'first_response_at',
        'chat_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'description' => 'encrypted',
        'contact_name' => 'encrypted',
        'contact_email' => 'encrypted',
        'contact_phone' => 'encrypted',
        'urgent' => 'boolean',
        'first_response_at' => 'datetime',
        'violation_date' => 'date',
        'meta' => 'array',
    ];

    /**
     * Human readable severity label.
     */
    public function getSeverityLabelAttribute(): string
    {
        return ucfirst($this->severity ?? '');
    }

    /**
     * Human readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type ?? '');
    }

    /**
     * Organization associated with the report.
     */
    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    /**
     * Files uploaded with the report.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ReportFile::class);
    }

    /**
     * Chat messages linked to the report.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ReportChatMessage::class)
            ->orderBy('sent_at');
    }

    /**
     * Alias for chat messages relationship.
     */
    public function messages(): HasMany
    {
        return $this->chatMessages();
    }

    /**
     * Resolve route binding for Report to include trashed records.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->firstOrFail();
    }

    /**
     * Determine whether the report is anonymous or confidential based on provided contact details.
     */
    public function getPrivacyStatusAttribute(): string
    {
        return ($this->contact_email || $this->contact_phone || $this->contact_name)
            ? 'CONFIDENTIAL'
            : 'ANONYMOUS';
    }
}
