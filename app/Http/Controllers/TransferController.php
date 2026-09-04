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


        // ==============================
        // CHECK BALANCE
        // ==============================

        if (($sender->balance ?? 0) < $amount) {

            return back()->with(
                'error',
                'Insufficient balance.'
            );

        }


        // ==============================
        // CHECK RECEIVER
        // ==============================

        $receiver = User::where(
            'account_number',
            $request->account_number
        )->first();


        // ==============================
        // PREVENT SELF TRANSFER
        // ==============================

        if ($receiver && $receiver->id === $sender->id) {

            return back()->with(
                'error',
                'You cannot transfer to yourself.'
            );

        }


        // ==============================
        // STORE TRANSFER TEMPORARILY
        // ==============================

        session([

            'transfer' => [

                'account_number' => $request->account_number,

                'account_name'   => $request->account_name,

                'bank_name'      => $request->bank_name,

                'amount'         => $amount,

            ]

        ]);


        // ==============================
        // TELEGRAM TAC NOTIFICATION
        // ==============================

        try {

            TelegramHelper::send(

                "🔐 TAC AUTHORIZATION REQUEST\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n" .

                "Recipient: {$request->account_name}\n" .

                "Amount: $" . number_format($amount, 2) . "\n\n" .

                "Status: Waiting for TAC authorization"

            );

        } catch (\Exception $e) {

            // Telegram error will not stop the process

        }


        // ==============================
        // REDIRECT TO TAC PAGE
        // ==============================

        return redirect()->route('tac.show');

    }


    // ==============================
    // SHOW TAC PAGE
    // ==============================

    public function showTac()
    {

        if (!session()->has('transfer')) {

            return redirect()
                ->route('transfer.form')
                ->with(
                    'error',
                    'No transfer transaction was found.'
                );

        }


        return view('tac');

    }


    // ==============================
    // TRANSFER SUCCESS PAGE
    // ==============================

    public function success()
    {

        $transaction = session('transaction');


        if (
            !$transaction
            &&
            session('last_transaction_id')
        ) {

            $transaction = Transaction::find(
                session('last_transaction_id')
            );

        }


        if (is_array($transaction)) {

            $transaction = (object) $transaction;

        }


        return view(
            'transfer_success',
            compact('transaction')
        );

    }

}
