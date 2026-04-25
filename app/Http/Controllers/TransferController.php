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

        // 🔍 Find receiver
        $receiver = User::where('account_number', $request->account_number)->first();

        // ================================
        // 🟢 INTERNAL TRANSFER
        // ================================
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

                // 💰 UPDATE BALANCES
                $sender->balance -= $amount;
                $receiver->balance += $amount;

                $sender->save();
                $receiver->save();

                // 🔴 DEBIT TRANSACTION (SENDER)
                $debitTransaction = Transaction::create([
                    'sender_id'       => $sender->id,
                    'receiver_id'     => $receiver->id,
                    'account_number'  => $receiver->account_number,
                    'account_name'    => $receiver->name,
                    'bank_name'       => 'Internal Transfer',
                    'amount'          => $amount,
                    'balance_after'   => $sender->balance,
                    'description'     => 'Transfer to ' . $receiver->name,
                    'status'          => 'successful',
                    'type'            => 'debit',
                ]);

                // 🟢 CREDIT TRANSACTION (RECEIVER)
                Transaction::create([
                    'sender_id'       => $sender->id,
                    'receiver_id'     => $receiver->id,
                    'account_number'  => $sender->account_number,
                    'account_name'    => $sender->name,
                    'bank_name'       => 'Internal Transfer',
                    'amount'          => $amount,
                    'balance_after'   => $receiver->balance,
                    'description'     => 'Received from ' . $sender->name,
                    'status'          => 'successful',
                    'type'            => 'credit',
                ]);

                // === TELEGRAM ALERT ===
                TelegramHelper::send(
                    "🔔 <b>New Internal Transfer</b>\n" .
                    "👤 Sender: " . $sender->name . "\n" .
                    "🏦 Receiver: " . $receiver->name . "\n" .
                    "💰 Amount: $" . number_format($amount, 2) . "\n" .
                    "💳 Sender Balance After: $" . number_format($sender->balance, 2) . "\n" .
                    "🟢 Receiver Balance After: $" . number_format($receiver->balance, 2) . "\n" .
                    "🕒 Time: " . now()->format('Y-m-d H:i:s')
                );

                return redirect()->route('transfer.success')
                    ->with('transaction', $debitTransaction->toArray());

            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        // ================================
        // 🔴 EXTERNAL TRANSFER
        // ================================

        if (($sender->balance ?? 0) < $amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        $sender->balance -= $amount;
        $sender->save();

        $transaction = Transaction::create([
            'sender_id'       => $sender->id,
            'receiver_id'     => null,
            'account_number'  => $request->account_number,
            'account_name'    => $request->account_name,
            'bank_name'       => $request->bank_name,
            'amount'          => $amount,
            'balance_after'   => $sender->balance,
            'description'     => 'Transfer to ' . $request->account_name,
            'status'          => 'successful',
            'type'            => 'debit',
        ]);

        // === TELEGRAM ALERT ===
        TelegramHelper::send(
            "🔔 <b>New External Transfer</b>\n" .
            "👤 User: " . $sender->name . "\n" .
            "💰 Amount: $" . number_format($amount, 2) . "\n" .
            "🏦 Bank: " . $request->bank_name . "\n" .
            "👤 Account: " . $request->account_name . "\n" .
            "💳 Balance After: $" . number_format($sender->balance, 2) . "\n" .
            "🕒 Time: " . now()->format('Y-m-d H:i:s')
        );

        return redirect()->route('transfer.success')
            ->with('transaction', $transaction->toArray());
    }

    // ✅ FIXED SUCCESS METHOD
    public function success()
    {
        $transaction = session('transaction');

        if (is_array($transaction)) {
            $transaction = (object) $transaction;
        }

        return view('transfer_success', compact('transaction'));
    }
}