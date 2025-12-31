<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'org_id',
        'active',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Get the organization the user belongs to.
     */
    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    /**
     * Private notes authored on reports.
     */
    public function reportNotes(): HasMany
    {
        return $this->hasMany(ReportNote::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPlatformAdmin(): bool
    {
        return $this->role === 'platform_admin';
    }

    public function isOrgAdmin(): bool
    {
        return in_array($this->role, ['org_admin', 'executive_admin'], true);
    }

    public function isOrgUser(): bool
    {
        return $this->isOrgAdmin() || in_array($this->role, ['org_user', 'security_lead', 'reviewer'], true);
    }

    /**
     * Check if the user has any of the provided roles.
     *
     * @param  string|array<int, string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($this->role, (array) $roles, true);
    }

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            if ($user->org_id) {
                $org = $user->org()->lockForUpdate()->first();

                if ($org) {
                    $org->increment('seats_used');
                }
            }
        });
    }

    /**
     * Resolve URL for the user's profile photo with fallback avatar.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            return $this->makePublicUrl('/storage/'.$this->profile_photo_path);
        }

        return $this->makePublicUrl('/assets/images/avatar-default.svg');
    }

    /**
     * Build an absolute URL using the current request host when possible.
     */
    protected function makePublicUrl(string $path): string
    {
        $normalizedPath = '/'.ltrim($path, '/');

        if (app()->bound('request')) {
            $request = request();

            if ($request) {
                return rtrim($request->getSchemeAndHttpHost(), '/').$normalizedPath;
            }
        }

        return url($normalizedPath);
    }
}
