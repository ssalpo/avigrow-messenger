<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Telegram
{
    public static function sendMessage(string $chatId, string $message, bool $disableWebpagePreview = true): void
    {
        $apiToken = config('services.telegram.token');

        if(!trim($chatId)) {
            return;
        }

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html',
            'disable_web_page_preview' => $disableWebpagePreview
        ];

        Http::post("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
    }

    public static function sendImage(string $chatId, string $url, string $caption = ''): void
    {
        $apiToken = config('services.telegram.token');

        $data = [
            'chat_id' => $chatId,
            'photo' => $url,
            'caption' => $caption
        ];

        Http::post("https://api.telegram.org/bot$apiToken/sendPhoto", $data);
    }

    public static function sendMessageToExistIds(string $message, bool $disableWebpagePreview = true): void
    {
        foreach (config('services.telegram.ids') as $telegramId) {
            self::sendMessage($telegramId, $message, $disableWebpagePreview);
        }
    }

    public static function sendImageToExistIds(string $url, string $caption = ''): void
    {
        foreach (config('services.telegram.ids') as $telegramId) {
            self::sendImage($telegramId, $url, $caption);
        }
    }
}
