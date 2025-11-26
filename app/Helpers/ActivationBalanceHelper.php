<?php

namespace App\Helpers;

use App\Models\User;

class ActivationBalanceHelper
{
    public static function get($userId)
    {
        $user = User::find($userId);

        return $user ? ($user->activation_balance ?? 0) : 0;
    }
}