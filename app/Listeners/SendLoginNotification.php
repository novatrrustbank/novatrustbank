<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\TelegramHelper;

class SendLoginNotification
{
    public function handle(Login $event)
    {
        $user = $event->user;

        $message = "🔔 <b>New Login Detected</b>\n"
                 . "👤 User: {$user->name}\n"
                 . "📧 Email: {$user->email}\n"
                 . "🕒 Time: " . now()->format('Y-m-d H:i:s') . "\n"
                 . "🌐 Website: novatrustbank.onrender.com";

        TelegramHelper::send($message);
    }
}
