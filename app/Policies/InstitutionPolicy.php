<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstitutionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasPermission('manage-institutions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Institution $institution): bool
    {
        // Admins can view their own institution
        if ($user->isAdmin() && $user->institution_id === $institution->id) {
            return $user->hasPermission('manage-institutions');
        }

        // Super admins can view all institutions
        if ($user->isAdmin() && $user->hasPermission('manage-all-institutions')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->hasPermission('manage-institutions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Institution $institution): bool
    {
        // Admins can update their own institution
        if ($user->isAdmin() && $user->institution_id === $institution->id) {
            return $user->hasPermission('manage-institutions');
        }

        // Super admins can update all institutions
        if ($user->isAdmin() && $user->hasPermission('manage-all-institutions')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Institution $institution): bool
    {
        // Only super admins can delete institutions
        if ($user->isAdmin() && $user->hasPermission('manage-all-institutions')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Institution $institution): bool
    {
        // Only super admins can restore institutions
        if ($user->isAdmin() && $user->hasPermission('manage-all-institutions')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Institution $institution): bool
    {
        // Only super admins can permanently delete institutions
        if ($user->isAdmin() && $user->hasPermission('manage-all-institutions')) {
            return true;
        }

        return false;
    }
} 