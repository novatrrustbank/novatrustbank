<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;

class AdminController extends Controller
{
    // ✅ Admin Dashboard
    public function index()
    {
        // Fetch uploads, transactions, and users
        $uploads = Upload::latest()->take(20)->get();
        $transactions = Transaction::latest()->take(10)->get();
        $users = User::all();

        return view('admin.dashboard', compact('uploads', 'transactions', 'users'));
    }

    // ✅ View All Users (Balance Control Page)
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    // ✅ Add or Deduct User Balance
    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'action' => 'required|in:add,deduct',
        ]);

        $user = User::findOrFail($id);

        if ($request->action === 'add') {
            $user->balance += $request->amount;
        } else {
            // Prevent negative balance
            $user->balance = max(0, $user->balance - $request->amount);
        }

        $user->save();

        // Optionally record in transactions table
        Transaction::create([
            'user_id' => $user->id,
            'type' => $request->action === 'add' ? 'Credit' : 'Debit',
            'amount' => $request->amount,
            'status' => 'Completed',
            'description' => $request->action === 'add' ? 'Admin added funds' : 'Admin deducted funds',
        ]);

        return redirect()->back()->with('success', 'User balance updated successfully.');
    }
}
