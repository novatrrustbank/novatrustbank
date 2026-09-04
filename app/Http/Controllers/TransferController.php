<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionTac;
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
// VERIFY TAC AND PROCESS TRANSFER
// ==============================

public function verifyTac(Request $request)
{
    // ==============================
    // VALIDATE TAC INPUT
    // ==============================

    $request->validate([
        'tac_code' => 'required|string|size:6',
    ]);


    // ==============================
    // CHECK TRANSFER SESSION
    // ==============================

    $transfer = session('transfer');

    if (!$transfer) {

        return redirect()
            ->route('transfer.form')
            ->with(
                'error',
                'Transfer session has expired. Please start again.'
            );
    }


    $sender = Auth::user();


    // ==============================
    // FIND TAC FOR THIS USER
    // ==============================

    $tac = TransactionTac::where(
        'user_id',
        $sender->id
    )
    ->where(
        'code',
        $request->tac_code
    )
    ->latest()
    ->first();


    // ==============================
    // TAC NOT FOUND
    // ==============================

    if (!$tac) {

        try {

            TelegramHelper::send(

                "❌ INVALID TAC ATTEMPT\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n\n" .

                "Entered TAC: {$request->tac_code}\n\n" .

                "Status: TAC not found"

            );

        } catch (\Exception $e) {
        }


        return back()->with(
            'error',
            'Invalid TAC Code.'
        );
    }


    // ==============================
    // CHECK TAC ACTIVE
    // ==============================

    if (!$tac->is_active) {

        try {

            TelegramHelper::send(

                "⚠️ INACTIVE TAC ATTEMPT\n\n" .

                "User: {$sender->name}\n" .

                "TAC: {$request->tac_code}\n\n" .

                "Status: Authorization blocked"

            );

        } catch (\Exception $e) {
        }


        return back()->with(
            'error',
            'This TAC Code is inactive.'
        );
    }


    // ==============================
    // CHECK TAC EXPIRATION
    // ==============================

    if ($tac->isExpired()) {

        try {

            TelegramHelper::send(

                "⏰ TAC EXPIRED\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n\n" .

                "TAC: {$request->tac_code}\n\n" .

                "Status: Authorization failed"

            );

        } catch (\Exception $e) {
        }


        return back()->with(
            'error',
            'This TAC Code has expired.'
        );
    }


    // ==============================
    // CHECK TAC ALREADY USED
    // ==============================

    if ($tac->isUsed()) {

        try {

            TelegramHelper::send(

                "⚠️ USED TAC ATTEMPT\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n\n" .

                "TAC: {$request->tac_code}\n\n" .

                "Status: Authorization blocked"

            );

        } catch (\Exception $e) {
        }


        return back()->with(
            'error',
            'This TAC Code has already been used.'
        );
    }


    // ==============================
    // START EXTERNAL TRANSFER
    // ==============================

    try {

        DB::transaction(function () use (
            $transfer,
            $sender,
            $tac
        ) {

            // ==============================
            // GET FRESH SENDER DATA
            // ==============================

            $lockedSender = User::findOrFail(
                $sender->id
            );


            $amount = (float) $transfer['amount'];


            // ==============================
            // CHECK BALANCE AGAIN
            // ==============================

            if (
                (float) $lockedSender->balance
                <
                $amount
            ) {

                throw new \Exception(
                    'Insufficient balance.'
                );
            }


            // ==============================
            // DEBIT SENDER
            // ==============================

            $lockedSender->balance =
                (float) $lockedSender->balance
                -
                $amount;

            $lockedSender->save();


            // ==============================
            // CREATE EXTERNAL TRANSFER RECORD
            // ==============================

            $debitTransaction = Transaction::create([

                'sender_id' => $lockedSender->id,

                // External recipient
                'receiver_id' => null,

                'amount' => $amount,

                'balance_after' =>
                    $lockedSender->balance,

                // External account details
                'account_number' =>
                    $transfer['account_number'],

                'account_name' =>
                    $transfer['account_name'],

                'bank_name' =>
                    $transfer['bank_name'],

                'type' => 'debit',

                'description' =>
                    'External transfer to ' .
                    $transfer['account_name'],

                'status' => 'completed',

            ]);


            // ==============================
            // MARK TAC AS USED
            // ==============================

            $tac->used_at = now();

            $tac->is_active = false;

            $tac->save();


            // ==============================
            // SAVE SUCCESS TRANSACTION ID
            // ==============================

            session([
                'last_transaction_id' =>
                    $debitTransaction->id
            ]);

        });


    } catch (\Exception $e) {

        try {

            TelegramHelper::send(

                "❌ TRANSFER AUTHORIZATION FAILED\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n\n" .

                "Reason: {$e->getMessage()}"

            );

        } catch (\Exception $telegramError) {
        }


        return back()->with(
            'error',
            $e->getMessage()
        );
    }


    // ==============================
    // SEND SUCCESS TELEGRAM MESSAGE
    // ==============================

    try {

        TelegramHelper::send(

            "✅ TAC VERIFIED - TRANSFER COMPLETED\n\n" .

            "User: {$sender->name}\n" .

            "Account: {$sender->account_number}\n\n" .

            "Recipient: {$transfer['account_name']}\n" .

            "Recipient Account: {$transfer['account_number']}\n" .

            "Bank: {$transfer['bank_name']}\n" .

            "Amount: $" .

            number_format(
                (float) $transfer['amount'],
                2
            ) .

            "\n\nStatus: Completed"

        );

    } catch (\Exception $e) {
    }


    // ==============================
    // CLEAR TRANSFER SESSION
    // ==============================

    session()->forget('transfer');


    // ==============================
    // REDIRECT TO SUCCESS PAGE
    // ==============================

    return redirect()
        ->route('transfer.success')
        ->with(
            'success',
            'Transfer completed successfully.'
        );
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

