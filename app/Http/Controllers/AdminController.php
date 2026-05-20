<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminController extends Controller
{
    // ==========================
    // Admin Dashboard
    // ==========================
    public function dashboard()
    {
        $uploads = Upload::latest()->take(20)->get();
        $transactions = Transaction::latest()->take(10)->get();
        $users = User::all();

        return view('admin.dashboard', compact('uploads', 'transactions', 'users'));
    }

    public function historyUsers()
    {
        $users = User::latest()->get();
        return view('admin.history_users', compact('users'));
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

        return back()->with('success', 'Balance updated successfully.');
    }

    public function editUserNamePage($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        return view('admin.edit-user-name', compact('user'));
    }

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

    public function createUserPage()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'balance'  => 'required|numeric',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'balance'  => $request->balance,
            'activation_balance' => 0
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function editUserHistory($id)
    {
        $user = User::findOrFail($id);

        $transactions = Transaction::where('sender_id', $id)
            ->orWhere('receiver_id', $id)
            ->latest()
            ->get();

        return view('admin.update_history', compact('user', 'transactions'));
    }

    public function updateHistory(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->amount = $request->amount;
        $transaction->balance_after = $request->balance_after;
        $transaction->account_name = $request->account_name;
        $transaction->status = $request->status;
        $transaction->created_at = $request->created_at;

        $transaction->save();

        return back()->with('success', 'History updated successfully');
    }

    public function editUserPage($id)
    {
        $user = User::find($id);

        if (!$user) return back()->with('error', 'User not found.');

        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'name'    => 'required',
            'email'   => 'required|email',
            'balance' => 'required|numeric',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->balance = $request->balance;

        // PASSWORD UPDATE
        if ($request->password && $request->password !== '') {
            $request->validate([
                'password' => 'min:6'
            ]);

            $user->password = Hash::make($request->password);
        }

        // ==========================
        // PASSPORT PHOTO UPLOAD (FINAL WORKING SOLUTION)
        // ==========================
        if ($request->hasFile('passport_photo')) {

            // Delete old local file if it exists
            if ($user->passport_photo && !filter_var($user->passport_photo, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($user->passport_photo);
            }

            // Step 1: store locally (backup)
            $localPath = $request->file('passport_photo')
                ->store('passports', 'public');

            // Step 2: upload to Cloudinary (LIVE)
            $cloudinaryUrl = Cloudinary::upload(
                storage_path('app/public/' . $localPath),
                [
                    'folder' => 'passports'
                ]
            )->getSecurePath();

            // Step 3: SAVE CLOUDINARY URL (instant dashboard update)
            $user->passport_photo = $cloudinaryUrl;
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
