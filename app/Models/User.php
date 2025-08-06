<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'institution_id',
        'company',
        'permissions',
        'last_login_at',
        'is_active',
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
            'permissions' => 'array',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the institution that the user belongs to.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the credentials issued by this user.
     */
    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    /**
     * Get the employer verifications performed by this user.
     */
    public function employerVerifications()
    {
        return $this->hasMany(EmployerVerification::class, 'employer_user_id');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an employer.
     */
    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->hasPermission('manage-all-institutions');
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get user permissions with caching.
     */
    public function getPermissions(): array
    {
        return Cache::remember("user_permissions_{$this->id}", 300, function () {
            return $this->permissions ?? $this->getDefaultPermissions();
        });
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $permissions = $this->getPermissions();
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get default permissions based on role.
     */
    protected function getDefaultPermissions(): array
    {
        return match ($this->role) {
            'admin' => [
                'manage-credentials',
                'manage-users',
                'view-reports',
                'manage-institutions',
            ],
            'employer' => [
                'verify-credentials',
                'view-verification-history',
                'bulk-verify',
            ],
            default => [],
        };
    }

    /**
     * Update user permissions.
     */
    public function updatePermissions(array $permissions): void
    {
        $this->update(['permissions' => $permissions]);
        Cache::forget("user_permissions_{$this->id}");
    }

    /**
     * Add a permission to the user.
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->getPermissions();
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->updatePermissions($permissions);
        }
    }

    /**
     * Remove a permission from the user.
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->getPermissions();
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->updatePermissions(array_values($permissions));
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get user's display name.
     */
    public function getDisplayName(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get user's initials for avatar.
     */
    public function getInitials(): string
    {
        $name = trim($this->name);
        if (empty($name)) {
            return strtoupper(substr($this->email, 0, 2));
        }

        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        return substr($initials, 0, 2);
    }

    /**
     * Scope to filter by institution.
     */
    public function scopeForInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /**
     * Scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear permissions cache when user is updated
        static::updated(function ($user) {
            Cache::forget("user_permissions_{$user->id}");
        });

        // Clear permissions cache when user is deleted
        static::deleted(function ($user) {
            Cache::forget("user_permissions_{$user->id}");
        });
    }
}
