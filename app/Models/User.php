<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'account_number',
        'balance',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Auto-create account number + default balance
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {

            // Generate unique account number
            if (empty($user->account_number)) {
                do {
                    $account = '203' . rand(1000000, 9999999);
                } while (self::where('account_number', $account)->exists());

                $user->account_number = $account;
            }

            // Default balance
            if ($user->balance === null) {
                $user->balance = 0;
            }
        });
    }

    /**
     * Auto-hash password
     */
    public function setPasswordAttribute($value)
    {
        if (strlen($value) < 60 || !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Transaction relationships
     */
    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'recipient_id');
    }

    /**
     * CHAT SYSTEM RELATIONSHIPS
     * -------------------------------------
     */

    // Messages the user sent
    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    // Messages the user received
    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    /**
     * Unread chat messages
     */
    public function unreadChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id')
            ->where('is_read', 0);
    }
}
