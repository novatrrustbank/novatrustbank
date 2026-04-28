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
            'account_name'   => 'required|string',
            'bank_name'      => 'required|string',
            'amount'         => 'required|numeric|min:1',
        ]);

        $sender = Auth::user();
        $amount = $request->amount;

        $receiver = User::where('account_number', $request->account_number)->first();

        // ================= INTERNAL =================
        if ($receiver) {

            if ($receiver->id === $sender->id) {
                return back()->with('error', 'You cannot transfer to yourself.');
            }

            if (($sender->balance ?? 0) < $amount) {
                return back()->with('error', 'Insufficient balance.');
            }

            try {
                $sender = $sender->fresh();
                $receiver = $receiver->fresh();

                $sender->balance -= $amount;
                $receiver->balance += $amount;

                $sender->save();
                $receiver->save();

                $debitTransaction = Transaction::create([
                    'sender_id'      => $sender->id,
                    'receiver_id'    => $receiver->id,
                    'account_number' => $receiver->account_number,
                    'account_name'   => $receiver->name,
                    'bank_name'      => 'Internal Transfer',
                    'amount'         => $amount,
                    'balance_after'  => $sender->balance,
                    'description'    => 'Transfer to ' . $receiver->name,
                    'status'         => 'successful',
                    'type'           => 'debit',
                ]);

                Transaction::create([
                    'sender_id'      => $sender->id,
                    'receiver_id'    => $receiver->id,
                    'account_number' => $sender->account_number,
                    'account_name'   => $sender->name,
                    'bank_name'      => 'Internal Transfer',
                    'amount'         => $amount,
                    'balance_after'  => $receiver->balance,
                    'description'    => 'Received from ' . $sender->name,
                    'status'         => 'successful',
                    'type'           => 'credit',
                ]);

                // SAFE TELEGRAM
                try {
                    TelegramHelper::send(
                        "🔔 Internal Transfer\n" .
                        "Sender: {$sender->name}\n" .
                        "Receiver: {$receiver->name}\n" .
                        "Amount: $" . number_format($amount, 2)
                    );
                } catch (\Exception $e) {}

                session(['last_transaction_id' => $debitTransaction->id]);

                return redirect()->route('transfer.success')
                    ->with('transaction', $debitTransaction->toArray());

            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // ================= EXTERNAL =================

        if (($sender->balance ?? 0) < $amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        $sender->balance -= $amount;
        $sender->save();

        $transaction = Transaction::create([
            'sender_id'      => $sender->id,
            'receiver_id'    => null,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'bank_name'      => $request->bank_name,
            'amount'         => $amount,
            'balance_after'  => $sender->balance,
            'description'    => 'Transfer to ' . $request->account_name,
            'status'         => 'successful',
            'type'           => 'debit',
        ]);

        // SAFE TELEGRAM
        try {
            TelegramHelper::send(
                "🔔 External Transfer\n" .
                "User: {$sender->name}\n" .
                "Amount: $" . number_format($amount, 2)
            );
        } catch (\Exception $e) {}

        session(['last_transaction_id' => $transaction->id]);

        return redirect()->route('transfer.success')
            ->with('transaction', $transaction->toArray());
    }

    public function success()
    {
        $transaction = session('transaction');

        if (!$transaction && session('last_transaction_id')) {
            $transaction = Transaction::find(session('last_transaction_id'));
        }

        if (is_array($transaction)) {
            $transaction = (object) $transaction;
        }

        return view('transfer_success', compact('transaction'));
    }
}