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

            // Create unique account number
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
     * Chat relationships
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Inbox & unread messages shortcut
     */
    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class, 'user_id');
    }

    public function unreadMessages()
    {
        return $this->messages()->where('is_read', false);
    }
}
