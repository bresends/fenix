<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public static function sendTextMessage(string $number, string $message): array
    {
        return Http::withHeaders([
            'apikey' => config('services.whatsapp.apikey'),
        ])->post(
            config('services.whatsapp.url') . '/message/sendText/' . config('services.whatsapp.instance'),
            [
                'number' => '55' . $number,
                'text' => $message,
            ]
        )->json();
    }
}
