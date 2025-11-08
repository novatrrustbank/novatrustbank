<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class HistoryController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('sender_id', Auth::id())
            ->with('recipient')
            ->latest()
            ->get();

        return view('history', compact('transactions'));
    }
}
