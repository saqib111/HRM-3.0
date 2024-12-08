<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Route;

class ChangepassController extends Controller
{
    public function index()
    {
        return view('changepass');
    }

    public function changepassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters long.',
        ]);

        $user = $request->user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400); // Changed the key to 'error'
        }

        // Check if the new password is the same as the current password
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'New password cannot be the same as the current password.'], 400); // Changed the key to 'error'
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
            'userpass' => "1",
        ]);

        $userRole = auth()->user()->role;

        return response()->json(['message' => 'Password changed successfully', 'userRole' => $userRole], 200);
    }

}
