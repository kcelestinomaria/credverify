<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Credential;
use App\Models\Institution;
use App\Policies\UserPolicy;
use App\Policies\CredentialPolicy;
use App\Policies\InstitutionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Credential::class => CredentialPolicy::class,
        Institution::class => InstitutionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // User Management Gates
        Gate::define('manage-users', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-users');
        });

        Gate::define('view-users', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-users');
        });

        // Institution Management Gates
        Gate::define('manage-institutions', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-institutions');
        });

        Gate::define('manage-all-institutions', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('view-institutions', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-institutions');
        });

        // Credential Management Gates
        Gate::define('manage-credentials', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-credentials');
        });

        Gate::define('view-credentials', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-credentials');
        });

        Gate::define('create-credentials', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-credentials');
        });

        Gate::define('revoke-credentials', function ($user) {
            return $user->isAdmin() && $user->hasPermission('manage-credentials');
        });

        // Employer Verification Gates
        Gate::define('verify-credentials', function ($user) {
            return $user->isEmployer() && $user->hasPermission('verify-credentials');
        });

        Gate::define('view-verification-history', function ($user) {
            return $user->isEmployer() && $user->hasPermission('view-verification-history');
        });

        Gate::define('bulk-verify', function ($user) {
            return $user->isEmployer() && $user->hasPermission('bulk-verify');
        });

        // Reporting and Analytics Gates
        Gate::define('view-reports', function ($user) {
            return $user->isAdmin() && $user->hasPermission('view-reports');
        });

        Gate::define('export-data', function ($user) {
            return $user->isAdmin() && $user->hasPermission('export-data');
        });

        // System Administration Gates
        Gate::define('manage-system', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('view-audit-logs', function ($user) {
            return $user->isAdmin() && $user->hasPermission('view-audit-logs');
        });

        // Multi-tenant Gates
        Gate::define('access-institution', function ($user, $institutionId) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            
            return $user->institution_id === $institutionId;
        });

        // Permission-based Gates
        Gate::define('has-permission', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        Gate::define('has-any-permission', function ($user, $permissions) {
            return $user->hasAnyPermission($permissions);
        });

        Gate::define('has-all-permissions', function ($user, $permissions) {
            return $user->hasAllPermissions($permissions);
        });
    }
}
