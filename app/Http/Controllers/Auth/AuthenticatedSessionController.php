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
        // Manually authenticate the user by checking credentials
        $credentials = $request->only('email', 'password');  // Adjust this if you need other fields

        // Try to authenticate the user
        if (Auth::attempt($credentials)) {
            // Retrieve the authenticated user
            $user = auth()->user();

            // Check if the user's status is disabled (status = "0")
            if ($user->status === "0") {
                // Log out the user immediately since their account is disabled
                Auth::logout();

                // Redirect to login page with error message
                return redirect()->route('login')
                    ->withErrors(['status_disabled' => 'Your account has been disabled by the administrator. Please contact HR.']);
            }

            // Check if the user needs to change their password
            if ($user->userpass == "0") {
                return redirect()->route('change.password'); // Redirect to change password page
            }

            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();

            // Redirect based on the user's role
            switch ($user->role) {
                case "1":
                    return redirect()->intended(route('dashboard'));
                case "2":
                case "3":
                case "4":
                case "5":
                    return redirect()->intended(route('attendanceemployee.record'));
                default:
                    return redirect()->route('login')->withErrors(['role' => 'Unauthorized role']);
            }
        }

        // If authentication fails, redirect back to login with error message
        return redirect()->route('login')->withErrors(['credentials' => 'Invalid credentials.']);
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
