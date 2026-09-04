<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionTac;
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
        // PASSPORT PHOTO UPLOAD
        // ==========================
        if ($request->hasFile('passport_photo')) {

            // Upload directly to Cloudinary
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('passport_photo')->getRealPath(),
                [
                    'folder' => 'passports'
                ]
            )->getSecurePath();

            // Save Cloudinary URL to database
            $user->passport_photo = $uploadedFileUrl;
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    // ==========================
// TAC MANAGEMENT PAGE
// ==========================
public function tacManagement()
{
    $users = User::orderBy('name', 'ASC')->get();

    $tacs = TransactionTac::with('user')
        ->latest()
        ->get();

    return view(
        'admin.tac_management',
        compact('users', 'tacs')
    );
}


// ==========================
// CREATE TAC
// ==========================
public function createTac(Request $request)
{
    $request->validate([
        'user_id'    => 'required|exists:users,id',
        'code'       => 'required|string|min:4|max:20',
        'expires_at' => 'required|date',
    ]);


    TransactionTac::create([
        'user_id'    => $request->user_id,
        'code'       => $request->code,
        'expires_at' => $request->expires_at,
        'is_active'  => true,
    ]);


    return back()->with(
        'success',
        'TAC Code created successfully.'
    );
}


// ==========================
// ACTIVATE / DEACTIVATE TAC
// ==========================
public function toggleTac($id)
{
    $tac = TransactionTac::findOrFail($id);

    $tac->is_active = !$tac->is_active;

    $tac->save();


    return back()->with(
        'success',
        'TAC status updated successfully.'
    );
}


// ==========================
// DELETE TAC
// ==========================
public function deleteTac($id)
{
    $tac = TransactionTac::findOrFail($id);

    $tac->delete();


    return back()->with(
        'success',
        'TAC deleted successfully.'
    );
}

// ==========================
// DELETE USER
// ==========================
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
