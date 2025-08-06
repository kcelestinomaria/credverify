<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact your administrator.'
            ]);
        }

        // Check if user has the required role
        if (!$this->hasRole($user, $role)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }

    /**
     * Check if user has the required role.
     */
    protected function hasRole($user, string $role): bool
    {
        return match ($role) {
            'admin' => $user->isAdmin(),
            'employer' => $user->isEmployer(),
            'super-admin' => $user->isSuperAdmin(),
            default => false,
        };
    }
}
