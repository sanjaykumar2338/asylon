<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'org_code',
        'status',
        'default_locale',
        'created_by',
        'on_call_user_id',
        'enable_commendations',
        'enable_hr_reports',
        'enable_student_reports',
        'enable_ultra_private_mode',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'enable_commendations' => 'boolean',
        'enable_hr_reports' => 'boolean',
        'enable_student_reports' => 'boolean',
        'enable_ultra_private_mode' => 'boolean',
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

    /**
     * Generate a report submission URL for the organization.
     */
    public function reportUrl(bool $absolute = true): string
    {
        return route('report.by_code', ['org_code' => $this->org_code], $absolute);
    }

    /**
     * Regenerate the public report submission code for the organization.
     */
    public function regenerateReportCode(): void
    {
        $this->org_code = static::generateUniqueOrgCode();
        $this->save();
    }

    /**
     * Automatically generate a report code when creating or clearing the value.
     */
    protected static function booted(): void
    {
        static::creating(function (Org $org): void {
            if (blank($org->org_code)) {
                $org->org_code = static::generateUniqueOrgCode();
            }
        });

        static::updating(function (Org $org): void {
            if ($org->isDirty('org_code') && blank($org->org_code)) {
                $org->org_code = static::generateUniqueOrgCode();
            }
        });
    }

    /**
     * Generate a unique 6-character alphanumeric organization code.
     */
    protected static function generateUniqueOrgCode(int $length = 6): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (static::withTrashed()->where('org_code', $code)->exists());

        return $code;
    }

    /**
     * Allowed report types for this organization.
     *
     * @return array<string, string>
     */
    public function enabledTypes(): array
    {
        $types = [];

        if ($this->enable_student_reports ?? true) {
            $types['safety'] = __('Safety & Threat');
        }

        if ($this->enable_commendations) {
            $types['commendation'] = __('Commendation');
        }

        if ($this->enable_hr_reports) {
            $types['hr'] = __('HR Anonymous');
        }

        return $types;
    }
}
