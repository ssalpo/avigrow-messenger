<?php

namespace App\Services;

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

    public static function sendMessageToExistIds(string $message): void
    {
        logger('Before log');
        logger()->info([123344,454545]);
        logger(config('services.telegram.token'));

        logger('Before iterate');
        foreach (config('services.telegram.ids') as $telegramId) {
            self::sendMessage($telegramId, $message);
        }
    }
}
