<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Telegram
{
    public static function sendMessage(int $chatId, string $message): void
    {
        $apiToken = config('services.telegram.token');

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html'
        ];

        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
    }

    public static function sendImage(int $chatId, string $url, string $caption = ''): void
    {
        $apiToken = config('services.telegram.token');

        $data = [
            'chat_id' => $chatId,
            'photo' => $url,
            'caption' => $caption
        ];

        Http::post("https://api.telegram.org/bot$apiToken/sendPhoto", $data);
    }

    public static function sendMessageToExistIds(string $message): void
    {
        foreach (config('services.telegram.ids') as $telegramId) {
            self::sendMessage($telegramId, $message);
        }
    }
}
