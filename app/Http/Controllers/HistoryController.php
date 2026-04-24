<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('web')->id(); // force correct guard

        $transactions = Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history', compact('transactions'));
    }
}
