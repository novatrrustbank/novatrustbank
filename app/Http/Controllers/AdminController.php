<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function index()
    {
        $uploads = Upload::latest()->take(20)->get();
        $transactions = Transaction::latest()->take(10)->get();
        $users = User::all();

        return view('admin.dashboard', compact('uploads', 'transactions', 'users'));
    }

    public function users()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('admin.users', compact('users'));
    }

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

    public function users()
    {
    $users = User::all();
    return view('admin.users', compact('users'));
    }

// ==========================
// Show all users
// ==========================
    public function users()
    {
    $users = User::all();
    return view('admin.users', compact('users'));
    }

// ==========================
// Create user page
// ==========================
    public function createUserPage()
    {
    return view('admin.create-user');
    }

// ==========================
// Store new user
// ==========================
    public function storeUser(Request $request)
    {
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'password' => 'required|min:6',
        'balance' => 'required|numeric',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'balance' => $request->balance
    ]);

    return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

// ==========================
// Edit user page
// ==========================
    public function editUserPage($id)
    {
    $user = User::find($id);

    if (!$user) return back()->with('error', 'User not found.');

    return view('admin.edit-user', compact('user'));
    }

// ==========================
// Update user (name, email, phone, balance, password
// ==========================
    public function updateUser(Request $request)
    {
    $request->validate([
        'user_id' => 'required',
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'balance' => 'required|numeric',
    ]);

    $user = User::find($request->user_id);

    if (!$user) return back()->with('error', 'User not found.');

    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->balance = $request->balance;

    if ($request->password != null) {
        $request->validate(['password' => 'min:6']);
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with('success', 'User updated successfully.');
    }

// ==========================
// Delete user
// ==========================
    public function deleteUser(Request $request)
    {
    $user = User::find($request->user_id);

    if (!$user) return back()->with('error', 'User not found.');

    $user->delete();

    return back()->with('success', 'User deleted.');
    }
}
