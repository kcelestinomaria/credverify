<?php

namespace App\Providers;

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
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate for managing institutions
        Gate::define('manage-institutions', function ($user) {
            return $user->isAdmin();
        });

        // Gate for managing credentials
        Gate::define('manage-credentials', function ($user) {
            return $user->isAdmin() && $user->institution_id !== null;
        });

        // Gate for employer verification
        Gate::define('employer-verify', function ($user) {
            return $user->isEmployer();
        });
    }
}
