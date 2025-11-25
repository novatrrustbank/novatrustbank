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
}
