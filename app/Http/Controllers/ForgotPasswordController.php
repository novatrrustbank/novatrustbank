<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('forgot_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Email check
        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        // Old password check
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Old password is incorrect.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Success popup message
        return redirect('/login')->with('success', 'Password changed successfully.');
    }
}