<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Transaction;

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

        $receiver = User::where('account_number', $request->account_number)->first();

        // ❌ prevent self transfer
        if ($receiver && $receiver->id === $sender->id) {
            return back()->with('error', 'You cannot transfer to yourself.');
        }

        // ❌ insufficient balance check
        if (($sender->balance ?? 0) < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        try {

            DB::beginTransaction();

            $sender = $sender->fresh();
            $amount = $request->amount;

            // =========================
            // 🟢 INTERNAL TRANSFER
            // =========================
            if ($receiver) {

                $receiver = $receiver->fresh();

                // update balances
                $sender->balance -= $amount;
                $receiver->balance += $amount;

                $sender->save();
                $receiver->save();

                // 🔴 DEBIT (sender record)
                Transaction::create([
                    'sender_id'     => $sender->id,
                    'receiver_id'   => $receiver->id,
                    'account_number'=> $receiver->account_number,
                    'account_name'  => $receiver->name,
                    'bank_name'     => 'Internal Transfer',
                    'amount'        => $amount,
                    'balance_after' => $sender->balance,
                    'type'          => 'debit',
                    'description'   => 'Transfer to ' . $receiver->name,
                    'status'        => 'successful',
                ]);

                // 🟢 CREDIT (receiver record)
                Transaction::create([
                    'sender_id'     => $sender->id,
                    'receiver_id'   => $receiver->id,
                    'account_number'=> $sender->account_number,
                    'account_name'  => $sender->name,
                    'bank_name'     => 'Internal Transfer',
                    'amount'        => $amount,
                    'balance_after' => $receiver->balance,
                    'type'          => 'credit',
                    'description'   => 'Received from ' . $sender->name,
                    'status'        => 'successful',
                ]);
            }

            // =========================
            // 🔴 EXTERNAL TRANSFER
            // =========================
            else {

                $sender->balance -= $amount;
                $sender->save();

                Transaction::create([
                    'sender_id'     => $sender->id,
                    'receiver_id'   => null,
                    'account_number'=> $request->account_number,
                    'account_name'  => $request->account_name,
                    'bank_name'     => $request->bank_name,
                    'amount'        => $amount,
                    'balance_after' => $sender->balance,
                    'type'          => 'debit',
                    'description'   => 'Transfer to ' . $request->account_name,
                    'status'        => 'successful',
                ]);
            }

            DB::commit();

            return redirect()->route('transfer.success')
                ->with('success', 'Transfer completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function success()
    {
        return view('transfer_success');
    }
}
