<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Credential;
use Illuminate\Auth\Access\HandlesAuthorization;

class CredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->hasPermission('manage-credentials');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Credential $credential): bool
    {
        // Admins can view credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        // Employers can view any credential for verification purposes
        if ($user->isEmployer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->hasPermission('manage-credentials');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Credential $credential): bool
    {
        // Only admins can update credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Credential $credential): bool
    {
        // Only admins can delete credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        return false;
    }

    /**
     * Determine whether the user can revoke the credential.
     */
    public function revoke(User $user, Credential $credential): bool
    {
        // Only admins can revoke credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Credential $credential): bool
    {
        // Only admins can restore credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Credential $credential): bool
    {
        // Only admins can permanently delete credentials from their institution
        if ($user->isAdmin() && $user->institution_id === $credential->institution_id) {
            return $user->hasPermission('manage-credentials');
        }

        return false;
    }
} 