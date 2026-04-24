<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class HistoryController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where(function ($query) {
                // Show ONLY sender's debits
                $query->where('sender_id', Auth::id())
                      ->where('type', 'debit');

                // OR show ONLY receiver's credits
                $query->orWhere(function ($q) {
                    $q->where('receiver_id', Auth::id())
                      ->where('type', 'credit');
                });
            })
            ->latest()
            ->get();

        return view('history', compact('transactions'));
    }
}