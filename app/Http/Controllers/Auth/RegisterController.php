<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // 🔹 Generate unique account number
        $account_number = $this->generateAccountNumber();

        // 🔹 Create user with hashed password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
 // ✅ HASH HERE
            'account_number' => $account_number,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    private function generateAccountNumber()
    {
        do {
            $number = mt_rand(1000000000, 9999999999); // 10-digit number
        } while (User::where('account_number', $number)->exists());

        return $number;
    }
}
