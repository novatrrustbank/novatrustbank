<?php

namespace App\Helpers;

use App\Models\User;

class ActivationBalanceHelper
{
    public static function get($userId)
    {
        $user = User::find($userId);
        return $user->activation_balance ?? 0;
    }

    public static function set($userId, $amount)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $user->activation_balance = $amount;
        $user->save();

        return true;
    }
}