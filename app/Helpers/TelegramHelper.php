<?php

namespace App\Helpers;

class TelegramHelper
{
    public static function send($message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chat_id) {
            return false; // Prevent crash if env missing
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            "chat_id" => $chat_id,
            "text" => $message,
            "parse_mode" => "HTML",
        ];

        // ---- Use cURL instead of file_get_contents ---- //
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response; // Optional for debugging
    }
}
