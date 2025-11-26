<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ==========================
    // Admin Dashboard
    // ==========================
    public function index()
    {
        $uploads = Upload::latest()->take(20)->get();
        $transactions = Transaction::latest()->take(10)->get();
        $users = User::all();

        return view('admin.dashboard', compact('uploads', 'transactions', 'users'));
    }

    // ==========================
    // Show all users (LIST)
    // ==========================
    public function users()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('admin.users', compact('users'));
    }

    // ==========================
    // UPDATE BALANCE
    // ==========================
    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'balance' => 'required|numeric'
        ]);

        $user = User::findOrFail($id);
        $user->balance = $request->balance;
        $user->save();

        return redirect()->back()->with('success', 'Balance updated successfully.');
    }

    // ==========================
    // SHOW EDIT USER NAME PAGE
    // ==========================
    public function editUserNamePage($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        return view('admin.edit-user-name', compact('user'));
    }

    // ==========================
    // UPDATE USER NAME
    // ==========================
    public function updateUserName(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name' => 'required|string|max:255',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'User name updated successfully.');
    }

    // ==========================
    // CREATE USER PAGE
    // ==========================
    public function createUserPage()
    {
        return view('admin.create-user');
    }

    // ==========================
    // STORE NEW USER
    // ==========================
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required',
            'password' => 'required|min:6',
            'balance'  => 'required|numeric',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'balance'  => $request->balance
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    // ==========================
    // EDIT USER PAGE
    // ==========================
    public function editUserPage($id)
    {
        $user = User::find($id);

        if (!$user) return back()->with('error', 'User not found.');

        return view('admin.edit-user', compact('user'));
    }

    // ==========================
    // UPDATE USER (NAME, EMAIL, BALANCE, PASSWORD)
    // ==========================
    public function updateUser(Request $request)
{
    $request->validate([
        'user_id' => 'required',
        'name'    => 'required',
        'email'   => 'required|email',
        'balance' => 'required|numeric',
    ]);

    $user = User::find($request->user_id);

    if (!$user) return back()->with('error', 'User not found.');

    // Update fields
    $user->name = $request->name;
    $user->email = $request->email;
    $user->balance = $request->balance;  // ✅ This will now save correctly

    // Password update
    if ($request->password && $request->password !== '') {
        $request->validate([
            'password' => 'min:6'
        ]);

        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'User updated successfully.');
}

    // ==========================
    // DELETE USER
    // ==========================
    public function deleteUser(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) return back()->with('error', 'User not found.');

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
