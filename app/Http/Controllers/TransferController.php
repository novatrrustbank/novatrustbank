<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;
use App\Helpers\TelegramHelper;

class TransferController extends Controller
{
    public function showForm()
    {
        return view('transfer');
    }

    public function processTransfer(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'bank_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        // 🔍 Check if internal user exists
        $receiver = User::where('account_number', $request->account_number)->first();

        // ================================
        // 🟢 INTERNAL TRANSFER (FIXED)
        // ================================
        if ($receiver) {

            if ($receiver->id === $user->id) {
                return back()->with('error', 'You cannot transfer to yourself.');
            }

            if (($user->balance ?? 0) < $request->amount) {
                return back()->with('error', 'Insufficient balance.');
            }

            try {

                // Refresh values
                $user = $user->fresh();
                $receiver = $receiver->fresh();

                // Update balances safely
                $user->balance = ($user->balance ?? 0) - $request->amount;
                $receiver->balance = ($receiver->balance ?? 0) + $request->amount;

                $user->save();
                $receiver->save();

                // Sender transaction
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'sender_id' => $user->id,
                    'receiver_id' => $receiver->id,
                    'account_number' => $receiver->account_number,
                    'account_name' => $receiver->name,
                    'bank_name' => 'Internal Transfer',
                    'type' => 'debit',
                    'amount' => $request->amount,
                    'balance_after' => $user->balance,
                    'description' => 'Transfer to ' . $receiver->name,
                    'status' => 'successful',
                ]);

                // Receiver transaction
                Transaction::create([
                    'user_id' => $receiver->id,
                    'sender_id' => $user->id,
                    'receiver_id' => $receiver->id,
                    'account_number' => $receiver->account_number,
                    'account_name' => $receiver->name,
                    'bank_name' => 'Internal Transfer',
                    'type' => 'credit',
                    'amount' => $request->amount,
                    'balance_after' => $receiver->balance,
                    'description' => 'Received from ' . $user->name,
                    'status' => 'successful',
                ]);

                return redirect()->route('transfer.success')->with('transaction', $transaction);

            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // ================================
        // 🔴 EXTERNAL TRANSFER
        // ================================

        if (($user->balance ?? 0) < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        $user->balance = ($user->balance ?? 0) - $request->amount;
        $user->save();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'sender_id' => $user->id,
            'receiver_id' => null,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'type' => 'debit',
            'amount' => $request->amount,
            'balance_after' => $user->balance,
            'description' => 'Transfer to ' . $request->account_name,
            'status' => 'successful',
        ]);

        // Optional: disable if causing issues
        /*
        TelegramHelper::send(
            "💸 Transfer\nUser: {$user->name}\nAmount: {$request->amount}"
        );
        */

        return redirect()->route('transfer.success')->with('transaction', $transaction);
    }

    public function success()
    {
        if (!session()->has('transaction')) {
            return redirect('/transfer')->with('error', 'No recent transaction found.');
        }

        $transaction = session('transaction');
        return view('transfer_success', compact('transaction'));
    }
}