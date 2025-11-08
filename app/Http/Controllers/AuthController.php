<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ===== Show Login Form =====
    public function showLoginForm()
    {
        return view('login');
    }

    // ===== Process Login =====
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // ✅ Try hashed password first
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();

                // ✅ Redirect based on admin status
                if ($user->is_admin == 1) {
                    return redirect()->intended('/admin/dashboard');
                }

                return redirect()->intended('/dashboard');
            }

            // ✅ Fallback for plain-text password (demo use only)
            if ($user->password === $request->password) {
                Auth::login($user);
                $request->session()->regenerate();

                if ($user->is_admin == 1) {
                    return redirect()->intended('/admin/dashboard');
                }

                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // ===== Logout =====
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ===== Show Register Form =====
    public function showRegisterForm()
    {
        return view('register');
    }

    // ===== Register New User =====
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'balance' => 100000.00, // optional default
            'is_admin' => 0, // default normal user
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
