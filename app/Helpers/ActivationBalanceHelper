<?php

namespace App\Helpers;

class ActivationBalanceHelper
{
    protected static $file = 'activation_balances.json';

    protected static function load()
    {
        $path = storage_path('app/' . self::$file);

        if (!file_exists($path)) {
            file_put_contents($path, json_encode([]));
        }

        return json_decode(file_get_contents($path), true);
    }

    protected static function save($data)
    {
        $path = storage_path('app/' . self::$file);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function get($userId)
    {
        $data = self::load();
        return $data[$userId] ?? 500; // default $500 if none exists
    }

    public static function set($userId, $amount)
    {
        $data = self::load();
        $data[$userId] = $amount;
        self::save($data);
    }
}
