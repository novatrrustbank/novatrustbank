<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('sender_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history', compact('transactions'));
    }
}
