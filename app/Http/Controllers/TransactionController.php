<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $transactions = Transaction::where(function ($query) use ($userId) {
                // 🔴 Show ONLY sender's debit records
                $query->where('sender_id', $userId)
                      ->where('type', 'debit');
            })
            ->orWhere(function ($query) use ($userId) {
                // 🟢 Show ONLY receiver's credit records
                $query->where('receiver_id', $userId)
                      ->where('type', 'credit');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history', compact('transactions'));
    }
}
