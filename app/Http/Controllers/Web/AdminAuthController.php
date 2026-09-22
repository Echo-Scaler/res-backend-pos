<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            return $this->redirectBasedOnRole($user);
        }

        return view('admin.auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasAnyRole(['OWNER', 'MANAGER', 'CASHIER', 'STAFF'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Access denied. You do not have permission to access the POS system.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect users to their dedicated dashboard based on role.
     */
    public function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->hasRole('OWNER')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('MANAGER')) {
            return redirect()->route('manager.dashboard');
        }

        if ($user->hasRole('CASHIER')) {
            return redirect()->route('cashier.dashboard');
        }

        if ($user->hasRole('STAFF')) {
            return redirect()->route('staff.dashboard');
        }

        Auth::logout();

        return redirect()->route('admin.login')->withErrors([
            'email' => 'Your account does not have a designated POS role assigned.',
        ]);
    }

    /**
     * Log the user out of the portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been successfully logged out.');
    }
}
