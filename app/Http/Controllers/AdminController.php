<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Credential;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Check if user has admin permissions
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Get statistics based on user's institution
        $stats = $this->getDashboardStats($user);

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display user management page.
     */
    public function users()
    {
        $user = auth()->user();
        
        // Check if user can manage users
        if (!Gate::allows('manage-users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        $users = User::query()
            ->when(!$user->isSuperAdmin(), function ($query) use ($user) {
                return $query->where('institution_id', $user->institution_id);
            })
            ->with('institution')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display user details.
     */
    public function showUser(User $user)
    {
        $currentUser = auth()->user();
        
        // Check if user can view this specific user
        if (!$currentUser->can('view', $user)) {
            abort(403, 'You do not have permission to view this user.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser()
    {
        $user = auth()->user();
        
        if (!Gate::allows('create', User::class)) {
            abort(403, 'You do not have permission to create users.');
        }

        $institutions = Institution::all();
        $roles = ['admin', 'employer'];

        return view('admin.users.create', compact('institutions', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        $user = auth()->user();
        
        if (!Gate::allows('create', User::class)) {
            abort(403, 'You do not have permission to create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,employer',
            'institution_id' => 'nullable|exists:institutions,id',
            'company' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        // Set default permissions based on role
        $validated['permissions'] = $this->getDefaultPermissionsForRole($validated['role']);
        
        // Ensure non-super admins can only create users in their institution
        if (!$user->isSuperAdmin()) {
            $validated['institution_id'] = $user->institution_id;
        }

        $newUser = User::create($validated);

        Log::info('User created', [
            'created_by' => $user->id,
            'new_user_id' => $newUser->id,
            'new_user_email' => $newUser->email,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user.
     */
    public function editUser(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->can('update', $user)) {
            abort(403, 'You do not have permission to edit this user.');
        }

        $institutions = Institution::all();
        $roles = ['admin', 'employer'];

        return view('admin.users.edit', compact('user', 'institutions', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->can('update', $user)) {
            abort(403, 'You do not have permission to update this user.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,employer',
            'institution_id' => 'nullable|exists:institutions,id',
            'company' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Ensure non-super admins can only update users in their institution
        if (!$currentUser->isSuperAdmin()) {
            $validated['institution_id'] = $currentUser->institution_id;
        }

        $user->update($validated);

        Log::info('User updated', [
            'updated_by' => $currentUser->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->can('delete', $user)) {
            abort(403, 'You do not have permission to delete this user.');
        }

        // Prevent self-deletion
        if ($currentUser->id === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        Log::info('User deleted', [
            'deleted_by' => $currentUser->id,
            'deleted_user_id' => $user->id,
            'deleted_user_email' => $user->email,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Display system settings page.
     */
    public function settings()
    {
        $user = auth()->user();
        
        if (!Gate::allows('manage-system')) {
            abort(403, 'You do not have permission to access system settings.');
        }

        return view('admin.settings');
    }

    /**
     * Display audit logs.
     */
    public function auditLogs()
    {
        $user = auth()->user();
        
        if (!Gate::allows('view-audit-logs')) {
            abort(403, 'You do not have permission to view audit logs.');
        }

        // This would typically fetch from an audit log table
        $logs = collect(); // Placeholder for audit logs

        return view('admin.audit-logs', compact('logs'));
    }

    /**
     * Get dashboard statistics.
     */
    private function getDashboardStats(User $user)
    {
        $query = Credential::query();
        
        // Filter by institution for non-super admins
        if (!$user->isSuperAdmin()) {
            $query->where('institution_id', $user->institution_id);
        }

        $totalCredentials = $query->count();
        $verifiedCredentials = $query->where('is_verified', true)->count();
        $thisMonthCredentials = $query->whereMonth('created_at', now()->month)->count();
        $totalVerifications = $query->where('verification_count', '>', 0)->sum('verification_count');

        return [
            'total_credentials' => $totalCredentials,
            'verified_credentials' => $verifiedCredentials,
            'this_month_credentials' => $thisMonthCredentials,
            'total_verifications' => $totalVerifications,
            'verification_rate' => $totalCredentials > 0 ? round(($verifiedCredentials / $totalCredentials) * 100, 1) : 0,
        ];
    }

    /**
     * Get default permissions for a role.
     */
    private function getDefaultPermissionsForRole(string $role): array
    {
        return match ($role) {
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
}
