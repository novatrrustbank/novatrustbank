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

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        // Deduct balance
        $user->balance -= $request->amount;
        $user->save();

        // Create transaction record
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

        // === TELEGRAM ALERT === //
        TelegramHelper::send(
            "?? <b>New Transfer / Withdrawal</b>\n" .
            "?? User: " . $user->name . "\n" .
            "?? Email: " . $user->email . "\n" .
            "?? Amount: $" . number_format($request->amount, 2) . "\n" .
            "?? Bank: " . $request->bank_name . "\n" .
            "?? Account Name: " . $request->account_name . "\n" .
            "?? Account Number: " . $request->account_number . "\n" .
            "?? Balance After: $" . number_format($user->balance, 2) . "\n" .
            "?? Time: " . now()->format('Y-m-d H:i:s') . "\n" .
            "?? novatrustbank.onrender.com"
        );

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
