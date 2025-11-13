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
     * Boot method to auto-generate account number and default balance.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generate unique account number if not set
            if (empty($user->account_number)) {
                do {
                    $account = '203' . rand(1000000, 9999999); // e.g. 2031234567
                } while (self::where('account_number', $account)->exists());

                $user->account_number = $account;
            }

            // Set default balance
            if ($user->balance === null) {
                $user->balance = 0;
            }
        });
    }

    /**
     * Automatically hash password when setting.
     */
    public function setPasswordAttribute($value)
    {
        // Only hash if not already hashed
        if (strlen($value) < 60 || !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Relationship: transactions sent by the user.
     */
    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    /**
     * Relationship: transactions received by the user.
     */
    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'recipient_id');
    }

//
public function messages()
{
    return $this->hasMany(\App\Models\Message::class, 'user_id');
}

public function unreadMessages()
{
    return $this->messages()->where('is_read', false);
}
}
