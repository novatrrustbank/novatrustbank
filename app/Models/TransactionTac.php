<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionTac extends Model
{
    protected $table = 'transaction_tacs';


    protected $fillable = [

        'user_id',

        'code',

        'expires_at',

        'used_at',

        'is_active',

    ];


    protected $casts = [

        'expires_at' => 'datetime',

        'used_at' => 'datetime',

        'is_active' => 'boolean',

    ];


    // ==============================
    // TAC BELONGS TO USER
    // ==============================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    // ==============================
    // CHECK IF TAC EXPIRED
    // ==============================

    public function isExpired(): bool
    {
        return now()->greaterThan(
            $this->expires_at
        );
    }


    // ==============================
    // CHECK IF TAC HAS BEEN USED
    // ==============================

    public function isUsed(): bool
    {
        return !is_null(
            $this->used_at
        );
    }


    // ==============================
    // CHECK IF TAC IS VALID
    // ==============================

    public function isValid(): bool
    {
        return

            $this->is_active

            &&

            !$this->isExpired()

            &&

            !$this->isUsed();

    }

}
