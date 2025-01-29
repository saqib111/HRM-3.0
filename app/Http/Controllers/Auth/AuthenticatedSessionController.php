<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ManageIpRestriction;
use App\Models\WhiteListIPs;
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
            $manage_ip_restrictions = ManageIpRestriction::where('user_id', '=', $user->id)->first();
            $restrictedUser = $manage_ip_restrictions->status;

            if ($restrictedUser == "0") {
                // Define the allowed IPs
                $fetchIPS = WhiteListIPs::pluck('ip_address'); // Extract only the 'ip_address' column
                $allowedIPS = $fetchIPS->toArray(); // Convert the collection to an array
                // $allowedIPS = ["49.156.37.182", "103.189.186.145", "202.93.15.164", "103.189.186.156", "103.25.92.130"];

                // Get the requesting IP address
                $ipAddress = $request->header('X-Forwarded-For')
                    ?? $request->header('CF-Connecting-IP')
                    ?? $request->header('X-Real-IP')
                    ?? $request->ip();

                // Check if the IP address is allowed
                if (!in_array($ipAddress, $allowedIPS)) {
                    // Restrict login if IP is not allowed
                    Auth::logout(); // Logout the user
                    return response()->json([
                        'message' => 'Access denied, unauthorized ip address'
                    ], 403);
                }
            }

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
