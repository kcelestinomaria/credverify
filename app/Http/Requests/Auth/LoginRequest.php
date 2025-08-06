<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if user exists and is active
        $user = User::where('email', $this->email)->first();

        if ($user && !$user->isActive()) {
            $this->handleFailedLogin($user);
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact your administrator.'
            ]);
        }

        // Check if account is locked
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is temporarily locked. Please try again later.'
            ]);
        }

        // Attempt authentication
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->handleFailedLogin($user);
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Clear rate limiting on successful login
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Handle failed login attempts.
     */
    protected function handleFailedLogin(?User $user): void
    {
        if ($user) {
            // Increment failed login attempts
            $failedAttempts = $user->failed_login_attempts + 1;
            $user->update(['failed_login_attempts' => $failedAttempts]);

            // Lock account after 5 failed attempts for 30 minutes
            if ($failedAttempts >= 5) {
                $user->update([
                    'locked_until' => now()->addMinutes(30),
                ]);

                Log::warning('Account locked due to multiple failed login attempts', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $this->ip(),
                    'failed_attempts' => $failedAttempts,
                ]);
            }

            Log::warning('Failed login attempt', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'failed_attempts' => $failedAttempts,
            ]);
        } else {
            Log::warning('Failed login attempt for non-existent user', [
                'email' => $this->email,
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
