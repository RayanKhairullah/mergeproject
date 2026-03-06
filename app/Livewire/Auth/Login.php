<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string')]
    public string $login = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public bool $passwordVisible = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Determine if login is email or username
        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$fieldType => $this->login, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if user has 2FA enabled
        if ($user->hasTwoFactorEnabled()) {
            Auth::logout();

            session()->put([
                'login.id' => $user->id,
                'login.remember' => $this->remember,
            ]);

            return $this->redirect(route('two-factor.challenge'), navigate: true);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Role-based redirect
        $redirectRoute = $this->getRedirectRoute($user);
        $this->redirectIntended(default: $redirectRoute, navigate: true);
    }

    /**
     * Get redirect route based on user role
     */
    protected function getRedirectRoute($user): string
    {
        // Check if user has admin-related roles
        if ($user->hasAnyRole(['super-admin', 'admin', 'sdm'])) {
            return route('admin.index', absolute: false);
        }

        // Default to dashboard for regular users
        return route('dashboard', absolute: false);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }
}
