<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLog extends Model
{
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_logs';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'user_id',
        'case_id',
        'action',
        'actor_type',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Organization context for the log entry.
     */
    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    /**
     * User actor recorded on the log entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Case/report relationship for the log entry.
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'case_id');
    }

    /**
     * Scope logs for a specific organization.
     */
    public function scopeForOrg($query, ?int $orgId)
    {
        return $query->where('org_id', $orgId);
    }
}
