<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class SettingController extends Controller
{
    //Show index
    public function index()
    {
        $user = Auth::user();
        return view('setting', compact('user'));
    }

    // Update profile image
    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate the image
        ]);

        $user = Auth::user(); // Get the currently logged-in user

        // Handle the uploaded image
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image'); // Get the uploaded file

            // Save the image using the helper function
            $defaultimg = uploadImage($uploadedFile); // Pass the file, not the name

            // If the user already has an image, optionally delete the old one
            if ($user->image !== "default_profile_picture.png") {
                $imagePath = public_path('uploads/' . $user->image);
            } elseif (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Update the user's image in the database
            $user->image = $defaultimg; // Extract just the filename
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile image updated successfully!');
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'errors' => ['current_password' => ['The current password is incorrect.']]
            ], 422);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);

    }

}
