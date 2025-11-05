<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Org extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'status',
        'created_by',
        'on_call_user_id',
    ];

    /**
     * Users that belong to the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Alert contacts configured for the organization.
     */
    public function alertContacts(): HasMany
    {
        return $this->hasMany(OrgAlertContact::class);
    }

    /**
     * Reports submitted for the organization.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Audit log entries associated with the organization.
     */
    public function auditLogEntries(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * User that created the organization.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * On-call reviewer assigned to urgent alerts.
     */
    public function onCallReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'on_call_user_id');
    }
}
