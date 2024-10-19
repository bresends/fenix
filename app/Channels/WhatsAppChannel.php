<?php

namespace App\Channels;

use App\Services\WhatsappService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toWhatsApp($notifiable);

        WhatsappService::sendTextMessage($notifiable->phone_number, $message['message']);
    }
}
