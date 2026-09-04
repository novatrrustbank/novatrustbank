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
    // ==============================
    // SHOW TRANSFER FORM
    // ==============================

    public function showForm()
    {
        return view('transfer');
    }


    // ==============================
    // PROCESS TRANSFER
    // ==============================

    public function processTransfer(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string',
            'account_name'   => 'required|string',
            'bank_name'      => 'required|string',
            'amount'         => 'required|numeric|min:1',
        ]);


        $sender = Auth::user();

        $amount = (float) $request->amount;


        // ==============================
        // CHECK BALANCE
        // ==============================

        if ((float) ($sender->balance ?? 0) < $amount) {

            return back()->with(
                'error',
                'Insufficient balance.'
            );

        }


        // ==============================
        // PREVENT SELF TRANSFER ONLY
        // ==============================

        if (
            !empty($sender->account_number)
            &&
            $request->account_number === $sender->account_number
        ) {

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

                'account_name' => $request->account_name,

                'bank_name' => $request->bank_name,

                'amount' => $amount,

            ]

        ]);


        // ==============================
        // TELEGRAM NOTIFICATION
        // ==============================

        try {

            TelegramHelper::send(

                "🔐 TAC AUTHORIZATION REQUEST\n\n" .

                "User: {$sender->name}\n" .

                "Account: {$sender->account_number}\n" .

                "Recipient: {$request->account_name}\n" .

                "Recipient Account: {$request->account_number}\n" .

                "Bank: {$request->bank_name}\n" .

                "Amount: $" .
                number_format($amount, 2) .

                "\n\nStatus: Waiting for TAC authorization"

            );

        } catch (\Exception $e) {

            // Telegram error will not stop transfer

        }


        // ==============================
        // REDIRECT TO TAC
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
    // VERIFY TAC
    // ==============================

    public function verifyTac(Request $request)
    {
        // ==============================
        // VALIDATE TAC
        // ==============================

        $request->validate([
            'tac_code' => 'required|string|size:6',
        ]);


        // ==============================
        // GET TRANSFER SESSION
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


        if (!$sender) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your session has expired.'
                );

        }


        // ==============================
        // FIND TAC
        // ==============================

        $tac = TransactionTac::where(
            'user_id',
            $sender->id
        )
        ->where(
            'code',
            $request->tac_code
        )
        ->latest('id')
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

            return back()->with(
                'error',
                'This TAC Code is inactive.'
            );

        }


        // ==============================
        // CHECK TAC EXPIRATION
        // ==============================

        if ($tac->isExpired()) {

            return back()->with(
                'error',
                'This TAC Code has expired.'
            );

        }


        // ==============================
        // CHECK TAC ALREADY USED
        // ==============================

        if ($tac->isUsed()) {

            return back()->with(
                'error',
                'This TAC Code has already been used.'
            );

        }


        // ==============================
        // GET AMOUNT
        // ==============================

        $amount = (float) $transfer['amount'];


        // ==============================
        // PROCESS TRANSFER
        // ==============================

        try {

            /*
            |--------------------------------------------------------------------------
            | GET FRESH SENDER
            |--------------------------------------------------------------------------
            */

            $freshSender = User::findOrFail(
                $sender->id
            );


            /*
            |--------------------------------------------------------------------------
            | CHECK BALANCE AGAIN
            |--------------------------------------------------------------------------
            */

            if (
                (float) $freshSender->balance
                <
                $amount
            ) {

                return back()->with(
                    'error',
                    'Insufficient balance.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE NEW BALANCE
            |--------------------------------------------------------------------------
            */

            $newBalance =
                (float) $freshSender->balance
                -
                $amount;


            /*
            |--------------------------------------------------------------------------
            | DEBIT SENDER
            |--------------------------------------------------------------------------
            */

            DB::table('users')
                ->where(
                    'id',
                    $freshSender->id
                )
                ->update([

                    'balance' => $newBalance,

                    'updated_at' => now(),

                ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE EXTERNAL TRANSACTION
            |--------------------------------------------------------------------------
            */

            $debitTransaction = Transaction::create([

                'sender_id' => $freshSender->id,

                /*
                | External account.
                | There is NO NovaTrust user receiver.
                */

                'receiver_id' => null,

                'amount' => $amount,

                'balance_after' => $newBalance,

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


            /*
            |--------------------------------------------------------------------------
            | MARK TAC AS USED
            |--------------------------------------------------------------------------
            */

            $tac->used_at = now();

            $tac->is_active = false;

            $tac->save();


            /*
            |--------------------------------------------------------------------------
            | SAVE TRANSACTION ID
            |--------------------------------------------------------------------------
            */

            session([

                'last_transaction_id' =>
                    $debitTransaction->id

            ]);


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
                'Transfer failed: ' .
                $e->getMessage()
            );

        }


        // ==============================
        // SUCCESS TELEGRAM MESSAGE
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
                    $amount,
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
        // REDIRECT TO SUCCESS
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
        $transaction = null;


        if (session('last_transaction_id')) {

            $transaction = Transaction::find(
                session('last_transaction_id')
            );

        }


        return view(
            'transfer_success',
            compact('transaction')
        );
    }
}
