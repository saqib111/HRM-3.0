<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('index');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate the session to prevent session fixation
        $request->session()->regenerate();

        // Check the user's role and redirect accordingly
        $user = auth()->user();

        if ($user->role === "1") {
            // Redirect to dashboard for admin or role 1
            return redirect()->intended(route('dashboard'));
        } elseif ($user->role === "2") {
            // Redirect to attendance record page for employee role 5
            return redirect()->intended(route('attendanceemployee.record'));
        } elseif ($user->role === "3") {
            // Redirect to attendance record page for employee role 5
            return redirect()->intended(route('attendanceemployee.record'));
        } elseif ($user->role === "4") {
            // Redirect to attendance record page for employee role 5
            return redirect()->intended(route('attendanceemployee.record'));
        } elseif ($user->role === "5") {
            // Redirect to attendance record page for employee role 5
            return redirect()->intended(route('attendanceemployee.record'));
        }

        // Optionally, handle cases where the role is neither 1 nor 5
        return redirect()->route('login')->withErrors(['role' => 'Unauthorized role']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
