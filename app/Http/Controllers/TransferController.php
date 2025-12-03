<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;

class TransferController extends Controller
{
    // ===== Show Transfer Form =====
    public function showForm()
    {
        return view('transfer');
    }

    // ===== Process Transfer =====
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
	\App\Helpers\TelegramHelper::send(
    "💸 <b>New Transfer / Withdrawal</b>\n" .
    "👤 User: " . auth()->user()->name . "\n" .
    "💵 Amount: $" . number_format($request->amount, 2) . "\n" .
    "🏦 Bank: " . $request->bank_name . "\n" .
    "👤 Account Name: " . $request->account_name . "\n" .
    "🔢 Account Number: " . $request->account_number . "\n" .
    "🕒 Time: " . now()->format('Y-m-d H:i:s') . "\n" .
    "🌐 novatrustbank.onrender.com"
);


        // Redirect with transaction data
        return redirect()->route('transfer.success')->with('transaction', $transaction);
    }

    // ===== Show Success Page =====
    public function success()
    {
        if (!session()->has('transaction')) {
            return redirect('/transfer')->with('error', 'No recent transaction found.');
        }

        $transaction = session('transaction');
        return view('transfer_success', compact('transaction'));
    }
}
