<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Http;
use App\Helpers\TelegramHelper;

class SendLoginNotification
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // ===== GET REAL USER IP =====
        $ip = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->ip();

        // If multiple IPs are returned, use the first
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Localhost fallback (Render sometimes sends 127.0.0.1)
        if ($ip === '::1' || $ip === '127.0.0.1') {
            $ip = '8.8.8.8'; // Google public IP (so location api doesn’t break)
        }

        // ===== GET LOCATION FROM IP =====
        $location = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,city,query")->json();

        $country = $location['status'] === 'success' ? $location['country'] : 'Unknown';
        $city    = $location['status'] === 'success' ? $location['city'] : 'Unknown';

        // ===== TELEGRAM MESSAGE =====
        $message = 
            "?? <b>New Login Detected</b>\n" .
            "?? User: {$user->name}\n" .
            "?? Email: {$user->email}\n" .
            "?? Country: {$country}\n" .
            "?? City: {$city}\n" .
            "?? IP Address: {$ip}\n" .
            "?? Time: " . now()->format('Y-m-d H:i:s') . "\n" .
            "?? Website: novatrustbank.onrender.com";

        // SEND MESSAGE
        TelegramHelper::send($message);
    }
}
