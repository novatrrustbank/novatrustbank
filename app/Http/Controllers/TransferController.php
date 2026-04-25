<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user = Auth::user();

        // 🔍 Find receiver
        $receiver = User::where('account_number', $request->account_number)->first();

        // ================================
        // 🟢 INTERNAL TRANSFER
        // ================================
        if ($receiver) {

            if ($receiver->id === $user->id) {
                return back()->with('error', 'You cannot transfer to yourself.');
            }

            if (($user->balance ?? 0) < $request->amount) {
                return back()->with('error', 'Insufficient balance.');
            }

            try {

                // Refresh
                $user = $user->fresh();
                $receiver = $receiver->fresh();

                // Update balances
                $user->balance -= $request->amount;
                $receiver->balance += $request->amount;

                $user->save();
                $receiver->save();

                // 💥 SINGLE TRANSACTION RECORD (IMPORTANT FIX)
                $transaction = Transaction::create([
                    'sender_id'      => $user->id,
                    'receiver_id'    => $receiver->id,
                    'account_number'  => $receiver->account_number,
                    'account_name'    => $receiver->name,
                    'bank_name'       => 'Internal Transfer',
                    'amount'          => $request->amount,
                    'balance_after'   => $user->balance,
                    'description'     => 'Transfer to ' . $receiver->name,
                    'status'          => 'successful',
                ]);

                return redirect()->route('transfer.success')
                    ->with('transaction', $transaction);

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

        $user->balance -= $request->amount;
        $user->save();

        $transaction = Transaction::create([
            'sender_id'      => $user->id,
            'receiver_id'    => null,
            'account_number'  => $request->account_number,
            'account_name'    => $request->account_name,
            'bank_name'       => $request->bank_name,
            'amount'          => $request->amount,
            'balance_after'   => $user->balance,
            'description'     => 'Transfer to ' . $request->account_name,
            'status'          => 'successful',
        ]);

        return redirect()->route('transfer.success')
            ->with('transaction', $transaction);
    }

    public function success()
    {
        if (!session()->has('transaction')) {
            return redirect('/transfer')
                ->with('error', 'No recent transaction found.');
        }

        return view('transfer_success', [
            'transaction' => session('transaction')
        ]);
    }
}
