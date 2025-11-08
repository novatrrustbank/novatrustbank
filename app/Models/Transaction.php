<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'receiver_id',
        'account_number',
        'account_name',
        'bank_name',
        'type',
        'amount',
        'balance_after',
        'description',
        'status',
    ];

    /**
     * Each transaction belongs to a sender (user).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Each transaction belongs to a receiver (user).
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * For general user relationship (optional).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
