<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Authenticate the user
            $request->authenticate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->isActive()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact your administrator.'
                ]);
            }

            // Check if account is locked
            if ($user->locked_until && $user->locked_until->isFuture()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account is temporarily locked. Please try again later.'
                ]);
            }

            // Reset failed login attempts on successful login
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_login_user_agent' => $request->userAgent(),
            ]);

            // Regenerate session for security
            $request->session()->regenerate();

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect based on user role
            $redirectRoute = $this->getRedirectRoute($user);
            
            return redirect()->intended($redirectRoute);

        } catch (ValidationException $e) {
            // Handle validation errors
            throw $e;
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Login error', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'An error occurred during login. Please try again.'
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Log logout
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        // Logout and invalidate session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the appropriate redirect route based on user role.
     */
    protected function getRedirectRoute($user): string
    {
        return match ($user->role) {
            'admin' => route('dashboard'),
            'employer' => route('employer.dashboard'),
            default => route('dashboard'),
        };
    }
}
