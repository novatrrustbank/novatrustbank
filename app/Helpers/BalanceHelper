<?php

namespace App\Helpers;

class BalanceHelper
{
    public static function getBalance($userId)
    {
        $path = storage_path('app/user_balances.json');

        if (!file_exists($path)) {
            file_put_contents($path, '{}');
        }

        $data = json_decode(file_get_contents($path), true);
        return $data[$userId] ?? 0;
    }

    public static function setBalance($userId, $amount)
    {
        $path = storage_path('app/user_balances.json');

        if (!file_exists($path)) {
            file_put_contents($path, '{}');
        }

        $data = json_decode(file_get_contents($path), true);
        $data[$userId] = $amount;
        file_put_contents($path, json_encode($data));
    }
}
