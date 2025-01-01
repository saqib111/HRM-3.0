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
    public function store(LoginRequest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:8'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = auth()->user();

            // Check if user is disabled
            if ($user->status === "0") {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account has been disabled by the administrator. Please contact HR.',
                ], 402);
            }

            // Check if user needs to change their password
            if ($user->userpass == "0") {
                return response()->json([
                    'redirect_url' => route('change.password'),
                    'message' => 'Please change your password.',
                ], 402);
            }

            // Regenerate session and determine redirection
            $request->session()->regenerate();
            $redirectRoute = match ($user->role) {
                "1" => route('dashboard'),
                "2", "3", "4", "5" => route('attendanceemployee.record'),
                default => null,
            };

            // Handle unauthorized role
            if (!$redirectRoute) {
                return response()->json([
                    'message' => 'Unauthorized role',
                ], 403);
            }

            return response()->json([
                'redirect_url' => $redirectRoute,
                'message' => 'Login successful',
            ], 200);
        }

        // Invalid credentials response
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
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
