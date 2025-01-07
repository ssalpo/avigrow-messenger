<?php

namespace App\Services\Avito\Message\Handlers;

use App\Services\Telegram;

class AvitoMessageImageHandler implements AvitoMessageHandler
{
    public function handle(array $content, array $params): void
    {
        $message = 'Фото';
        $url = $content['image']['sizes']['1280x960'];

        Telegram::sendMessageToExistIds(
            view('telegram.webhook-message', $params + ['message' => $message])->render()
        );

        Telegram::sendImageToExistIds($url);
    }
}
